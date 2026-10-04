# Changelog

Vse pomembnejše spremembe aplikacije Uploader App so zapisane v obratno kronološkem vrstnem redu.

## 2026-10-03

### Dokumenti – arhiviranje

* Dodano je bilo popolnoma reverzibilno arhiviranje dokumentov.
* Dokument je mogoče arhivirati brez brisanja njegovega zapisa ali fizične datoteke.
* Dodan je ločen pogled **Arhiv** na `/documents/archive`.
* Dodani sta ločeni akciji **Arhiviraj** in **Obnovi**.
* Arhiviranje ni več izvedeno prek splošnega posodabljanja dokumenta.
* Dodani so podatki o času arhiviranja (`archived_at`) in uporabniku, ki je dokument arhiviral (`archived_by`).
* Dodana je povezava do uporabnika, ki je dokument arhiviral.
* Aktivni seznam dokumentov prikazuje samo ne-arhivirane dokumente.
* Arhiv prikazuje samo arhivirane dokumente.
* Iskanje in paginacija delujeta ločeno v aktivnem seznamu in arhivu.
* Arhivirani dokumenti ostanejo vidni in jih je mogoče prenesti, če ima uporabnik ustrezne pravice.
* Brisanje ostaja ločena in trajna operacija.

### Pravice uporabnikov

Dodane so nove pravice:

* `archive any document`
* `archive own documents`
* `restore any document`
* `restore own documents`

Privzeta vloga `client` lahko arhivira in obnovi svoje dokumente.

Vloga `admin` lahko arhivira in obnovi dokumente drugih uporabnikov.

Obstoječe pravice za ogled, nalaganje in brisanje dokumentov ostajajo ločene.

### Testiranje

Dodani so funkcionalni testi za:

* arhiviranje dokumenta;
* obnovitev dokumenta;
* ohranitev fizične datoteke pri arhiviranju;
* ogled in prenos arhiviranega dokumenta;
* omejitev pravic na lastne dokumente;
* administratorski dostop;
* ločevanje aktivnih dokumentov in arhiva;
* iskanje in paginacijo v arhivu;
* preprečitev arhiviranja prek splošnega `update` endpointa;
* obstoječe brisanje dokumentov.

### Laravel in PHP

* PHP je bil nadgrajen na **8.3+**.
* Laravel je bil nadgrajen na **12.x**.
* Posodobljene so bile povezane Composer odvisnosti.
* GitHub Actions testno okolje uporablja PHP 8.3.

### Frontend in uporabniški vmesnik

* Dodana je navigacija med **Dokumenti** in **Arhivom**.
* Dodane so akcije za arhiviranje in obnovitev.
* Dodana so potrditvena sporočila za arhiviranje in obnovitev.
* Izboljšano je pozicioniranje in prikaz Tooltip komponente.
* Posodobljeni so bili angleški in slovenski prevodi.
* Popravljen je bil `baseUrl` v `jsconfig.json` za pravilno razreševanje modulov.

### Dokumentacija in razvojno okolje

* Posodobljen je README z opisom novega načina arhiviranja.
* Dodana so navodila za preverjanje arhiviranja in izdelavo frontend builda.
* Zahteva za lokalno okolje je posodobljena na Node.js 22+.
* Dodana je dokumentacija za lokalno testiranje e-pošte z Mailpit.
* Dodan je `init.sh` za inicializacijo razvojnega/strežniškega okolja.
* Popravljena je pot do logotipa v README.

## 2026-10-02

### Posodobitev razvojnega okolja

* Urejena je bila inicializacija aplikacije in razvojnega okolja.
* Posodobljena je bila dokumentacija projekta.
* Pripravljene so bile spremembe za prehod na PHP 8.3 in Laravel 12.
* Posodobljeno je bilo CI/testno okolje.

---

## Opomba

Ta changelog opisuje spremembe, ki so bile v repozitoriju izvedene od 2. 10. 2026 dalje.

Dodani testi predstavljajo avtomatizirane preveritve funkcionalnosti. Uspeh izvajanja testov je treba potrditi z dejanskim zagonom:

```bash
php artisan test
```

oziroma s preverjanjem GitHub Actions.
