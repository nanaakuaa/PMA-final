<?php

namespace App\Services;

use App\Models\Password;
use App\Models\Folder;
use Illuminate\Support\Collection;

class ExportService
{
    public function __construct(
        private EncryptionService $encryptionService
    ) {}

    private function getExportData(): array
    {
        $passwords = Password::with(['folder', 'creator', 'updater'])
            ->get()
            ->map(function ($password) {
                $data = $password->toArray();
                try {
                    $data['password'] = $this->encryptionService->decrypt($password->password);
                } catch (\Throwable $e) {
                    $data['password'] = null;
                }

                if ($password->notes) {
                    try {
                        $data['notes'] = $this->encryptionService->decrypt($password->notes);
                    } catch (\Throwable $e) {
                        $data['notes'] = null;
                    }
                }

                return $data;
            })
            ->toArray();

        $folders = Folder::withCount('passwords')->get()->toArray();

        return [
            'exported_at' => now()->toDateTimeString(),
            'version' => '1.0',
            'folders' => $folders,
            'passwords' => $passwords,
        ];
    }

    public function exportAsJson(): string
    {
        return json_encode($this->getExportData(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    public function exportAsCsv(): string
    {
        $data = $this->getExportData();
        $output = "Title,Username,Password,URL,Folder,Notes,Created By,Updated By,Created At,Updated At\n";

        foreach ($data['passwords'] as $password) {
            $folder = $password['folder']['name'] ?? 'Uncategorized';
            $createdBy = $password['creator']['name'] ?? 'Unknown';
            $updatedBy = $password['updater']['name'] ?? 'Unknown';

            $output .= $this->csvRow([
                $password['title'] ?? '',
                $password['username'] ?? '',
                $password['password'] ?? '',
                $password['url'] ?? '',
                $folder,
                $password['notes'] ?? '',
                $createdBy,
                $updatedBy,
                $password['created_at'] ?? '',
                $password['updated_at'] ?? '',
            ]);
        }

        return $output;
    }

    public function exportAsXml(): string
    {
        $data = $this->getExportData();
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><mvault></mvault>');
        $xml->addAttribute('version', $data['version']);
        $xml->addAttribute('exported_at', $data['exported_at']);

        $foldersXml = $xml->addChild('folders');
        foreach ($data['folders'] as $folder) {
            $folderXml = $foldersXml->addChild('folder');
            $folderXml->addAttribute('id', $folder['id']);
            $folderXml->addChild('name', htmlspecialchars($folder['name']));
            $folderXml->addChild('description', htmlspecialchars($folder['description'] ?? ''));
            $folderXml->addChild('passwords_count', $folder['passwords_count'] ?? 0);
        }

        $passwordsXml = $xml->addChild('passwords');
        foreach ($data['passwords'] as $password) {
            $passwordXml = $passwordsXml->addChild('password');
            $passwordXml->addAttribute('id', $password['id']);
            $passwordXml->addChild('title', htmlspecialchars($password['title'] ?? ''));
            $passwordXml->addChild('username', htmlspecialchars($password['username'] ?? ''));
            $passwordXml->addChild('password', htmlspecialchars($password['password'] ?? ''));
            $passwordXml->addChild('url', htmlspecialchars($password['url'] ?? ''));
            $passwordXml->addChild('notes', htmlspecialchars($password['notes'] ?? ''));
            $passwordXml->addChild('folder_id', $password['folder_id'] ?? '');
            $passwordXml->addChild('created_at', $password['created_at'] ?? '');
            $passwordXml->addChild('updated_at', $password['updated_at'] ?? '');
            $passwordXml->addChild('created_by', $password['creator']['name'] ?? 'Unknown');
            $passwordXml->addChild('updated_by', $password['updater']['name'] ?? 'Unknown');
        }

        return $xml->asXML();
    }

    public function exportAsPdf(): string
    {
        $data = $this->getExportData();

        $html = view('exports.passwords-pdf', compact('data'))->render();

        $pdf = \PDF::loadHTML($html);
        $pdf->setPaper('a4', 'landscape');

        return $pdf->output();
    }

    private function csvRow(array $fields): string
    {
        return implode(',', array_map(function ($field) {
            return '"' . str_replace('"', '""', $field) . '"';
        }, $fields)) . "\n";
    }
}
