# Kvalitetsfund og korrigerende handlinger

Proof of concept til CleanManagers projektforslag. Det er en selvstændig web-app, der følger et kvalitetsfund hele vejen:
**inspektion → fund → korrigerende handling → bevis → verificeret løsning**.

Bygget med Laravel 13, Blade, Tailwind 4, SQLite og Pest.

## Om udviklingen

Mit tidligere projekt [laravel-auth-foundations](https://github.com/Abishiyambas/laravel-auth-foundations)
har været guide for strukturen: AuthController, routes grupperet pr. controller, `<x-layout>`/`<x-card>`,
én seeder pr. model og tests i Pest. Selve koden er udviklet med hjælp fra Claude Code (AI). Jeg har
gennemgået og testet koden.

## Kom i gang

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate:fresh --seed   # demo-data
php artisan storage:link           # fotos
npm install && npm run build
php artisan serve
```

Log ind med en af demo-brugerne. Adgangskoden er `password` for alle.

| Bruger | Rolle | Må |
|---|---|---|
| sara@demo.dk / soeren@demo.dk | Supervisor (verifier) | Alt, inkl. godkende/afvise løsninger og administrere brugere, lokationer og skabeloner |
| ida@demo.dk | Inspektør | Udføre inspektioner, registrere fund og tildele handlinger |
| mads@demo.dk / lene@demo.dk | Medarbejder | Arbejde på egne handlinger og indsende bevis |

Kør tests med `php artisan test`.

## MVP – hvor ligger hvad

| Krav i forslaget | Implementering |
|---|---|
| **Inspektion:** lokationer og kontrolskabeloner | `LocationController`, `TemplateController`, `InspectionController` |
| **Fund:** kategori, alvorlighed, kommentar og foto | `FindingController`, `Finding` |
| **Handling:** ansvarlig og frist | `ActionController@store`, `CorrectiveAction::assign()` |
| **Status:** åben, i gang, afventer verificering, løst | `App\Enums\ActionStatus` (tilladte overgange) |
| **Dokumentation:** den ansvarlige indsender bevis | `CorrectiveAction::submitEvidence()`, tabellen `evidence` |
| **Verificering:** supervisor godkender eller afviser | `approve()` / `reject()`. Kun supervisor, aldrig egen løsning, og afvisning kræver en begrundelse |
| **Overblik og historik** | `DashboardController` med overskredne frister, gentagne fund (samme lokation og kategori inden for 90 dage), løsningstid og tabellen `action_logs` |

### Regler, der håndhæves (og testes)

- Alle handlinger har altid et fund, en ansvarlig, en frist og en historik (NOT NULL-fremmednøgler).
- Kun en autoriseret verifier (rollen supervisor) kan sætte status til *løst*, og kun fra *afventer verificering*.
- En verifier kan ikke godkende sin egen løsning.
- Kun den ansvarlige kan påbegynde en handling og indsende bevis.
- Nye brugere bliver altid oprettet som medarbejder, uanset hvad der sendes i requesten.
- Hver statusændring logges med bruger, tidspunkt, fra/til-status og kommentar.

## Struktur

```
app/Enums/ActionStatus.php        statusser og tilladte overgange
app/Models/CorrectiveAction.php   workflow (assign/start/submitEvidence/approve/reject)
app/Models/Finding.php            gentagne fund, filtre
app/Providers/AppServiceProvider  gates: inspect, assign, verify, manage
app/Http/Controllers/             Auth, Dashboard, Location, Template, Inspection, Finding, Action, User
resources/views/                  én mappe pr. ressource + components/
database/seeders/HistorySeeder    realistisk historik (kører workflowet med Carbon::setTestNow)
tests/Feature/DemoScenarioTest    hele demoen fra forslaget som én test
```

Se også [docs/demo.md](docs/demo.md) (demo-manuskript) og [docs/evaluering.md](docs/evaluering.md)
(skabelon til sammenligning, begrænsninger, driftsomkostninger og anbefaling).

## Afgrænsning

Med vilje uden for scope, som forslaget beskriver: komplekse scoringsstandarder, automatisk vurdering af renhed via fotos,
prædiktiv analyse og integration med CleanManager.
