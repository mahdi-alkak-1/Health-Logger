<?php
// Backend/models/Habit.php
require_once __DIR__ . '/Model.php';

class Habit extends Model
{
    private int $id;
    private int $user_id;
    private string $name;
    private string $entry_field;
    private string $unit;
    private int $target_value;
    private int $is_active;
    private string $created_at;
    private string $updated_at;

    protected static string $table = "habits";

    public function __construct(array $data)
    {
        $this->id           = (int)$data["id"];
        $this->user_id      = (int)$data["user_id"];
        $this->name         = $data["name"];
        $this->entry_field  = $data["entry_field"];
        $this->unit         = $data["unit"];
        $this->target_value = (int)$data["target_value"];
        $this->is_active    = (int)$data["is_active"];
        $this->created_at   = $data["created_at"];
        $this->updated_at   = $data["updated_at"];
    }

    public function getId(): int        { return $this->id; }
    public function getUserId(): int    { return $this->user_id; }
    public function getName(): string   { return $this->name; }
    public function getEntryField(): string { return $this->entry_field; }
    public function getUnit(): string   { return $this->unit; }
    public function getTargetValue(): ?int { return $this->target_value; }
    public function getIsActive(): int  { return $this->is_active; }

    public function toArray(): array
    {
        return [
            "id"           => $this->id,
            "user_id"      => $this->user_id,
            "name"         => $this->name,
            "entry_field"  => $this->entry_field,
            "unit"         => $this->unit,
            "target_value" => $this->target_value,
            "is_active"    => $this->is_active,
            "created_at"   => $this->created_at,
            "updated_at"   => $this->updated_at,
        ];
    }
}
