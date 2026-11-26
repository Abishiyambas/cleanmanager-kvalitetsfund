<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\CorrectiveAction;
use App\Models\Finding;
use App\Models\Inspection;
use App\Models\Location;
use App\Models\Template;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * Historiske data, så dashboardet har noget at vise. Hver handling kører gennem
 * det rigtige workflow; Carbon::setTestNow() "rejser i tid", så tidsstempler og
 * historik hænger sammen.
 *
 * Bemærk: Hovedindgang + "Glas og vinduer" har et løst fund for 3 uger siden,
 * så demoens nye fund på snavset indgangsglas bliver markeret som gentaget.
 */
class HistorySeeder extends Seeder
{
    private array $users;

    private Carbon $today;

    public function run(): void
    {
        $this->today = today();
        $this->users = User::pluck('id', 'email')->map(fn ($id) => User::find($id))->all();

        // [dage siden, lokation, kategori, alvorlighed, kommentar, ansvarlig, handling, frist (dage), forløb]
        $history = [
            [40, 'Toiletter 1. sal', 'Sanitet og forbrugsvarer', 'medium', 'Papirhåndklæder opbrugt i dametoilet.', 'lene', 'Fyld papirhåndklæder op og tjek lager.', 1, 'resolved:5'],
            [33, 'Kantine', 'Gulve', 'low', 'Krummer og pletter under vinduesborde.', 'mads', 'Støvsug og vask gulv under vinduesborde.', 2, 'resolved:20'],
            [21, 'Hovedindgang', 'Glas og vinduer', 'medium', 'Fingeraftryk på glasdør ved reception.', 'mads', 'Pudse glasdøre ved hovedindgang.', 1, 'resolved:6'],
            [14, 'Mødelokale Fyn', 'Overflader og inventar', 'low', 'Kaffepletter på mødebord.', 'lene', 'Aftør mødebord og stole.', 1, 'rejected-then-resolved:30'],
            [9, 'Kantine', 'Affald', 'high', 'Madaffald ikke tømt – lugtgener.', 'mads', 'Tøm madaffald og rengør beholder.', 1, 'resolved:3'],
            [5, 'Kantine', 'Gulve', 'medium', 'Klistret gulv ved kaffemaskine.', 'mads', 'Grundrengør gulv ved kaffemaskine.', 3, 'in_progress'],
            [2, 'Toiletter 1. sal', 'Sanitet og forbrugsvarer', 'high', 'Tom sæbedispenser på herretoilet.', 'lene', 'Fyld sæbedispenser og tjek de andre.', 1, 'awaiting'],
        ];

        foreach ($history as $row) {
            $this->createCase(...$row);
        }

        Carbon::setTestNow();
    }

    private function createCase(int $daysAgo, string $location, string $category, string $severity, string $comment,
        string $responsible, string $description, int $dueInDays, string $flow): void
    {
        $sara = $this->users['sara@demo.dk'];
        $ida = $this->users['ida@demo.dk'];
        $soeren = $this->users['soeren@demo.dk'];
        $worker = $this->users["$responsible@demo.dk"];

        $start = $this->today->copy()->subDays($daysAgo)->setTime(8, 30);
        Carbon::setTestNow($start);

        $location = Location::where('name', $location)->firstOrFail();
        $inspection = Inspection::create([
            'location_id' => $location->id,
            'template_id' => Template::first()->id,
            'inspector_id' => $ida->id,
        ]);
        $finding = Finding::create([
            'inspection_id' => $inspection->id,
            'location_id' => $location->id,
            'category_id' => Category::where('name', $category)->value('id'),
            'created_by' => $ida->id,
            'severity' => $severity,
            'comment' => $comment,
        ]);

        Carbon::setTestNow($start->copy()->addMinutes(15));
        $action = CorrectiveAction::assign($finding, $ida, [
            'responsible_id' => $worker->id,
            'description' => $description,
            'due_date' => $start->copy()->addDays($dueInDays)->toDateString(),
        ]);

        [$state, $hours] = array_pad(explode(':', $flow), 2, null);
        $at = fn (float $h) => Carbon::setTestNow($start->copy()->addMinutes((int) ($h * 60)));

        if ($state === 'in_progress') {
            $at(1);
            $action->start($worker);
        }

        if ($state === 'awaiting') {
            $at(1);
            $action->start($worker);
            $at(2);
            $action->submitEvidence($worker, 'Udført.', null);
        }

        if ($state === 'resolved') {
            $at($hours * 0.3);
            $action->start($worker);
            $at($hours * 0.8);
            $action->submitEvidence($worker, 'Opgaven er udført.', null);
            $at($hours);
            $action->approve($sara);
        }

        if ($state === 'rejected-then-resolved') {
            $at(2);
            $action->submitEvidence($worker, 'Bordet er tørret af.', null);
            $at(4);
            $action->reject($soeren, 'Der er stadig pletter på stolene. Tag dem også.');
            $at($hours - 2);
            $action->submitEvidence($worker, 'Stole og bord rengjort.', null);
            $at($hours);
            $action->approve($soeren, 'OK nu.');
        }
    }
}
