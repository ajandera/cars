<?php

namespace Core\Instacover\Models;

class Poptavka
{
    private ?int $id = null;

    private ?array $poptavka = null;

    private string $table;

    public function __construct(int $id, string $table)
    {
        $this->id = $id;
        $this->table = $table;
        $this->poptavka = $this->loadRow();
    }

    private string $state;

    private function loadRow(): ?array
    {
        $sql = coreDBSel("SELECT * FROM {$this->table} WHERE id = ?", [$this->id]);

        if ($sql && $sql->recordCount() > 0) {
            return $sql->fetchRow();
        } else {
            return null;
        }
    }

    public function saveSesionId(string $sessionId): bool
    {
        if (!$this->poptavka) {
            return false;
        }

        $edit = ['instacover_session_id' => ['value' => $sessionId]];
        $sql = coreDBEdit($this->table,$edit,"(id = '".$this->id."')",[],1,true);
        return $sql ? true : false;
    }

    public function saveDoneSatus(): bool
    {
        if (!$this->poptavka) {
            return false;
        }

        $edit = ['stav_customerinterested' => ['value' => 1], 'stav_photoscompleted' => ['vallue' => 1]];
        $sql = coreDBEdit($this->table,$edit,"(id = '".$this->id."')",[],1,true);
        return $sql ? true : false;
    }

    public function getPoptavkaSessionId(): ?string
    {
        return $this->poptavka['instacover_session_id'] ?? null;
    }

    public function getState(): string
    {
        return strtoupper($this->poptavka['stat']) ?? 'CZ';
    }
}