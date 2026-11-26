<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Student;
use Illuminate\Http\Request;
use Smalot\PdfParser\Parser as PdfParser;
use PhpOffice\PhpWord\IOFactory as WordFactory;
use Maatwebsite\Excel\Facades\Excel;

class RosterImportController extends Controller
{
    public function create(Group $group, Request $request)
    {
        abort_if($group->semester->user_id !== $request->user()->id, 403);
        return view('groups.roster', compact('group'));
    }

    public function store(Group $group, Request $request)
    {
        abort_if($group->semester->user_id !== $request->user()->id, 403);
        $request->validate([
            'roster_file' => 'nullable|mimes:pdf,csv,txt,xlsx,docx|max:10240',
            'manual_text' => 'nullable|string'
        ]);

        $text = $request->manual_text ?? '';

        if ($request->hasFile('roster_file')) {
            $file = $request->file('roster_file');
            $extension = strtolower($file->getClientOriginalExtension());
            $path = $file->getRealPath();

            if ($extension === 'pdf') {
                $parser = new PdfParser();
                $pdf = $parser->parseFile($path);
                $text .= "\n" . $pdf->getText();
            } elseif ($extension === 'docx') {
                $zip = new \ZipArchive;
                if ($zip->open($path) === true) {
                    if (($index = $zip->locateName('word/document.xml')) !== false) {
                        $text .= "\n" . strip_tags(str_replace(['<w:p>', '<w:p '], "\n<w:p>", $zip->getFromIndex($index)));
                    }
                    $zip->close();
                }
            } elseif (in_array($extension, ['csv', 'txt'])) {
                $text .= "\n" . file_get_contents($path);
            } elseif ($extension === 'xlsx') {
                $zip = new \ZipArchive;
                if ($zip->open($path) === true) {
                    if (($index = $zip->locateName('xl/sharedStrings.xml')) !== false) {
                        $text .= "\n" . strip_tags(str_replace(['<t>', '<t '], " <t>", $zip->getFromIndex($index)));
                    }
                    for ($i = 1; $i <= 3; $i++) {
                        if (($index = $zip->locateName("xl/worksheets/sheet{$i}.xml")) !== false) {
                            $text .= "\n" . strip_tags(str_replace(['<v>', '<v '], " <v>", $zip->getFromIndex($index)));
                        }
                    }
                    $zip->close();
                }
            }
        }

        if (empty(trim($text))) {
            return back()->with('error', 'No text or valid file provided.');
        }

        // Regex to match "103793 DARRYIELL KARLLEN"
        preg_match_all('/(\d{6})\s+([A-Za-z\s\@\.\']+)/', $text, $matches, PREG_SET_ORDER);
        
        $added = 0;
        foreach ($matches as $match) {
            $matrix = trim($match[1]);
            $name = trim(preg_replace('/\s+/', ' ', $match[2]));
            
            // Clean up common trailing artifacts if any
            $name = preg_replace('/(present|absent|late).*$/i', '', $name);
            $name = trim($name);
            
            if (strlen($name) > 2) {
                Student::updateOrCreate(
                    ['matrix_number' => $matrix, 'group_id' => $group->id],
                    ['name' => $name, 'email' => strtolower($matrix) . '@example.com']
                );
                $added++;
            }
        }

        return redirect()->route('groups.show', $group)->with('success', "Roster imported successfully! Found $added students.");
    }
}
