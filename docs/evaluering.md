# Evaluering – skabelon

Leverancerne kræver også en sammenligning med en manuel arbejdsgang, kendte fejlscenarier og begrænsninger,
driftsomkostninger samt en anbefaling om at fortsætte, justere eller stoppe. Udfyld dette efter test med brugere.
Tallene må **ikke** opfindes: uden pilot rapporteres effekten som uafklaret, sådan som forslaget siger.

## Succeskriterier

| Kriterium | Status | Evidens |
|---|---|---|
| Alle handlinger kan spores til fund, ansvarlig, frist og løsningshistorik | Opfyldt i design | NOT NULL-fremmednøgler, `action_logs`, `DemoScenarioTest` |
| Kun en autoriseret verifier kan markere en handling som løst | Opfyldt og testet | `AccessTest` |
| Mindst 90 % af evalueringsscenarierne gennemføres uden hjælp | _Ikke målt_ | Brugertest: antal testpersoner, scenarier og hvor mange der klarede dem uden hjælp |
| Brugere finder forsinkede og gentagne problemer hurtigere end i et regneark | _Ikke målt_ | Tidtagning: app vs. regneark, samme opgave |
| Løsningstid | _Uafklaret uden pilot_ | Dashboardet måler gennemsnit og median fra oprettelse til verificeret løsning |

## Sammenligning med manuel arbejdsgang (beskeder og regneark)

| | Manuelt | App |
|---|---|---|
| Hvem er ansvarlig? | | |
| Er fristen overskredet? | | |
| Er det udført, og hvem har tjekket det? | | |
| Er det sket før samme sted? | | |

## Kendte fejlscenarier og begrænsninger

- Ingen notifikationer (e-mail/push) ved tildeling eller afvisning. Brugeren skal selv åbne appen.
- "Gentaget fund" er en simpel regel (samme lokation og kategori inden for 90 dage) og fanger ikke lignende fund på tværs af kategorier.
- Ingen offline-tilstand. Det kræver netværk ude på lokationen.
- Fotos gemmes lokalt på serveren (`storage/app/public`) uden komprimering.
- Én organisation. Der er ingen multi-tenancy.
- Roller er grove (3 niveauer) og ikke pr. lokation.

## Driftsomkostninger (estimat – udfyld)

- Hosting: _fx lille VPS eller managed PHP-hosting_
- Lagerplads til fotos: _antal fund/md × gennemsnitlig filstørrelse_
- Vedligehold: _Laravel-opdateringer, backup_

## Anbefaling

_Fortsæt / justér / stop – med begrundelse fra punkterne ovenfor._
