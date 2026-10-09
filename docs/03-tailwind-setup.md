# 03 — Tailwind Setup
- created models : User, Tune, Task
    ```text
    User->HasMany->Tunes | Tune->BelongsTo->User
    Tune->Hasmany->Tasks | Task->BelongsTo->Tune
    ```
- added enum : ```phpTuneStatus.php```
    ```php
        public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::IN_PROGRESS => 'In Progress',
            self::COMPLETED => 'Completed'
        };
    }
    ```
- created dummy data: ```phpTuneFactory.php``` & ```phpTaskFactory.php```
    ```bash
    php artisan tinker
    $tune = App\Models\Tune::factory()->make(); 
    ````
    ```bash
    = App\Models\Tune {#7626
        status: App\TuneStatus {#7651
        +name: "PENDING",
        +value: "pending",
        },
        user_id: 2,
        title: "Dicta placeat aliquam porro enim quasi maiores molestiae.",
        description: "Sit magnam sit et illum neque repellendus. Tempora dolores sint ut tempore voluptatem ipsam aut.",
        links: "[\"http:\\/\\/koch.biz\\/\"]",
    }
    ```
- added test : ```phpTuneTest.php```
    ```bash
    vendor\bin\pest tests\Unit\TuneTest.php
    ```
    ```bash
   PASS  Tests\Unit\TuneTest
  ✓ it belongs to a user                                                                                                 0.24s  

  Tests:    1 passed (1 assertions)
  Duration: 0.39s
    ```

## Problems & Solutions

- pest was not reconized globally :
    ```bash
    pest tests\Unit\TuneTest.php ❌
    vendor\bin\pest tests\Unit\TuneTest.php ✅
    ```


## Notes

