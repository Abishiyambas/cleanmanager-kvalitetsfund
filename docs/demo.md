# Demo-manuskript (ca. 5 min)

Forslagets demo: *registrér snavset indgangsglas og tom sæbedispenser → tildel handlinger → indsend bevis → afvis én løsning → verificér → identificér et gentaget fund.*

Forberedelse: `php artisan migrate:fresh --seed` og `php artisan serve`. Brug gerne to browservinduer (et normalt og et privat), så du kan være to brugere på én gang.

1. **Ida (inspektør)** → *+ Ny inspektion* → Hovedindgang, skabelonen "Daglig kontrol – indgang og toiletter".
2. Ved "Indgangsglas …" → *Registrér fund* → alvorlighed Middel → upload et foto → Gem.
   Beskeden viser **"OBS: gentaget fund"**, fordi der var et glas-fund samme sted for 3 uger siden.
3. Tilbage til inspektionen → ved "Sæbedispensere er fyldt" → *Registrér fund* → alvorlighed Høj.
4. På hvert fund → *+ Tildel handling*: glas til **Mads**, sæbe til **Lene**, frist i dag eller i morgen.
5. **Mads** → Handlinger → *Mine åbne* → *Markér som i gang* → indsend bevis (tekst og foto).
6. **Lene** → indsend bevis.
7. **Sara (supervisor)** → Handlinger → *Afventer verificering*:
   - Glas: **Afvis** med begrundelsen "Striber i nederste hjørne". Status går tilbage til *I gang*.
   - Sæbe: **Godkend**. Status bliver *Løst*.
8. **Mads** indsender nyt bevis, og **Sara** godkender.
9. Vis **Løsningshistorik** på glas-handlingen: oprettet → i gang → bevis → afvist → bevis → godkendt.
10. Vis **Overblik**: *Gentagne fund* viser nu Hovedindgang · Glas og vinduer (2×), og der er løsningstid og overskredne frister.

Bonus, som viser adgangskontrol: log ind som Mads og prøv at åbne en andens handling. Der er ingen knapper til at indsende bevis eller verificere.

Samme forløb kører automatisk i `tests/Feature/DemoScenarioTest.php`.
