<?php

namespace Core\Instacover\Models;

class Zastava
{
    private ?int $id = null;

    private ?array $zastava = null;

    private string $table;

    public function __construct(int $id, string $table)
    {
        $this->id = $this->$id;
        $this->table = $table;
        $this->zastava = $this->loadRow();
    }

    private function loadRow(): ?array
    {
        $sql = coreDBSel("SELECT * FROM `" . $this->table . "` WHERE id = ?", [$this->id]);

        if ($sql && $sql->recordCount() > 0) {
            return $sql->fetchRow();
        } else {
            return null;
        }
    }

    public function saveSesionId(string $sessionId): bool
    {
        if (!$this->zastava) {
            return false;
        }

        $edit = ['session_id' => ['value' => $sessionId]];
        $sql = coreDBEdit($this->table,$edit,"(id = '".$this->id."')",array(),1,true);
        return $sql ? true : false;
    }

    public function getZastavaSessionId(): ?int
    {
        return $this->zastava['session_id'] ?? null;
    }
}