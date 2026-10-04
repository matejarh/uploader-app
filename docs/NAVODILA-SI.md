# Uploader App – navodila za uporabo

## 1. Namen aplikacije

Uploader App je aplikacija za varno nalaganje, organiziranje, pregledovanje in upravljanje dokumentov.

Dokumenti so organizirani glede na uporabnika, podjetje, leto in mapo.

Aplikacija omogoča:

* nalaganje dokumentov;
* pregled dokumentov;
* iskanje dokumentov;
* prenos dokumentov;
* označevanje dokumentov kot obdelanih;
* arhiviranje dokumentov;
* obnovitev arhiviranih dokumentov;
* brisanje dokumentov;
* upravljanje uporabnikov in podjetij za uporabnike z ustreznimi pravicami.

---

# 2. Prijava

Za uporabo aplikacije se prijavite z uporabniškim računom.

Po uspešni prijavi se prikaže nadzorna plošča.

Uporabniške pravice določajo, katere funkcije so uporabniku na voljo.

---

# 3. Dokumenti

Za pregled dokumentov odprite **Dokumenti**.

V seznamu so prikazani aktivni dokumenti.

Običajno so prikazani:

* datum;
* stranka;
* podjetje;
* leto;
* mapa;
* ime datoteke;
* status obdelave;
* razpoložljive operacije.

Uporabnik z običajnimi pravicami vidi samo dokumente, do katerih ima dostop.

Administrator lahko vidi dokumente drugih uporabnikov, če ima za to ustrezne pravice.

---

# 4. Nalaganje dokumenta

Za nalaganje novega dokumenta uporabite gumb **Naloži dokument**.

Izberite datoteko in sledite navodilom aplikacije.

Po uspešnem nalaganju je dokument dodan med aktivne dokumente.

Nov dokument ni arhiviran.

---

# 5. Iskanje dokumentov

V seznamu dokumentov lahko uporabite iskalno polje.

Iskanje omogoča hitrejše iskanje dokumentov glede na podatke, ki jih aplikacija podpira.

Iskanje v razdelku **Dokumenti** išče med aktivnimi dokumenti.

Iskanje v razdelku **Arhiv** išče med arhiviranimi dokumenti.

---

# 6. Ogled dokumenta

Za ogled dokumenta uporabite operacijo **Ogled**.

Ogled dokumenta ne spremeni njegovega statusa.

Arhiviran dokument je še vedno mogoče odpreti, če ima uporabnik pravico do ogleda dokumenta.

---

# 7. Prenos dokumenta

Za prenos dokumenta uporabite operacijo **Prenos** oziroma ikono za prenos.

Arhiviranje dokumenta ne vpliva na možnost prenosa.

Če ima uporabnik pravico do ogleda dokumenta, lahko praviloma prenese tudi arhiviran dokument.

---

# 8. Status »Obdelan«

Dokument ima lahko status **Obdelan**.

Ta status je ločen od arhiviranja.

To pomeni, da sta lahko dokument in njegova stanja na primer:

* neobdelan in aktiven;
* obdelan in aktiven;
* neobdelan in arhiviran;
* obdelan in arhiviran.

Arhiviranje ne spremeni statusa »Obdelan«.

---

# 9. Arhiviranje dokumenta

Arhiviranje je namenjeno dokumentom, ki jih trenutno ne potrebujete v glavnem seznamu, vendar jih želite ohraniti.

Pri aktivnem dokumentu izberite:

**Arhiviraj**

Aplikacija lahko pred arhiviranjem zahteva potrditev.

Po potrditvi:

1. dokument ni več prikazan v aktivnem seznamu;
2. dokument se premakne v **Arhiv**;
3. zapis dokumenta ostane v podatkovni zbirki;
4. fizična datoteka ostane shranjena;
5. dokument je mogoče pozneje obnoviti.

### Pomembno

**Arhiviranje ni brisanje.**

Pri arhiviranju se datoteka ne izbriše.

---

# 10. Arhiv

Za ogled arhiviranih dokumentov izberite:

**Arhiv**

oziroma odprite:

```text
/documents/archive
```

V arhivu so prikazani samo arhivirani dokumenti.

Arhivirani dokument lahko:

* odprete;
* prenesete;
* obnovite;
* izbrišete, če imate za to pravico.

---

# 11. Obnovitev dokumenta

Če želite arhiviran dokument ponovno prikazati med aktivnimi dokumenti, uporabite:

**Obnovi**

Po potrditvi se dokument vrne med aktivne dokumente.

Pri obnovitvi:

* `archived` se nastavi na `false`;
* datum arhiviranja se odstrani;
* uporabnik, ki je arhiviral dokument, se odstrani iz trenutnega statusa arhiviranja.

Datoteka ostane nespremenjena.

---

# 12. Brisanje dokumenta

**Brisanje in arhiviranje sta različni operaciji.**

Arhiviranje:

```text
dokument ostane shranjen
```

Brisanje:

```text
dokument se odstrani
datoteka se odstrani iz shrambe
```

Brisanje je zato treba uporabljati previdno.

Če dokumenta ne želite več prikazovati med aktivnimi dokumenti, vendar ga želite ohraniti, uporabite **Arhiviraj** in ne **Izbriši**.

---

# 13. Pravice običajnega uporabnika

Običajni uporabnik oziroma uporabnik z vlogo `client` ima lahko pravice za:

* nalaganje dokumentov;
* ogled lastnih dokumentov;
* brisanje lastnih dokumentov;
* arhiviranje lastnih dokumentov;
* obnovitev lastnih arhiviranih dokumentov.

Uporabnik ne more arhivirati ali obnoviti dokumenta drugega uporabnika, če nima ustrezne pravice.

---

# 14. Navodila za administratorje

Administrator ima širši dostop do dokumentov.

Administrator lahko glede na dodeljene pravice:

* pregleda dokumente drugih uporabnikov;
* išče dokumente;
* nalaga dokumente;
* arhivira dokumente drugih uporabnikov;
* obnovi arhivirane dokumente drugih uporabnikov;
* briše dokumente, če ima pravico do brisanja.

Administrator naj pri arhiviranju uporablja arhiv kot način organiziranja dokumentov in ne kot nadomestilo za brisanje.

---

# 15. Pravice za arhiviranje

Sistem uporablja štiri ločene pravice:

```text
archive any document
archive own documents
restore any document
restore own documents
```

### Archive own documents

Uporabnik lahko arhivira svoje dokumente.

### Archive any document

Uporabnik lahko arhivira tudi dokumente drugih uporabnikov.

### Restore own documents

Uporabnik lahko obnovi svoje arhivirane dokumente.

### Restore any document

Uporabnik lahko obnovi tudi arhivirane dokumente drugih uporabnikov.

---

# 16. Varnost in dostop

Pravica do arhiviranja sama po sebi ne daje pravice do ogleda dokumenta.

Uporabnik mora imeti tudi ustrezno pravico do dostopa do dokumenta.

Na ta način arhiviranje ne more uporabniku nenamerno omogočiti dostopa do dokumentov, ki jih sicer ne bi smel videti.

---

# 17. Iskanje in paginacija v arhivu

Arhiv podpira enako iskanje kot aktivni seznam.

Če je rezultatov veliko, se uporablja paginacija.

Pri premikanju med stranmi ostane uporabnik v arhivu.

Primer:

```text
Arhiv
  ↓
iskanje: račun
  ↓
stran 1
  ↓
stran 2
```

Uporabnik se ne vrne samodejno v aktivni seznam dokumentov.

---

# 18. Kaj se zgodi z datoteko pri arhiviranju?

Datoteka se ne premakne.

Ne spremeni se:

* ime datoteke;
* pot do datoteke;
* vsebina datoteke.

Spremeni se samo status dokumenta v podatkovni zbirki.

Zato je arhiviranje hitro in varno.

---

# 19. Priporočena uporaba

Priporočamo naslednji način dela:

### Aktivni dokumenti

V aktivnem seznamu naj bodo dokumenti, ki jih trenutno uporabljate.

### Arhiv

V arhiv premaknite dokumente, ki jih želite ohraniti, vendar jih ne potrebujete več v glavnem seznamu.

### Brisanje

Brisanje uporabljajte samo za dokumente, ki jih želite dejansko odstraniti iz sistema.

Praktično pravilo:

```text
Še potrebujem → Dokumenti

Ne uporabljam več, vendar moram hraniti → Arhiv

Ne potrebujem in ne želim hraniti → Izbriši
```

---

# 20. Hiter pregled funkcij

| Funkcija        | Aktivni dokument | Arhivirani dokument |
| --------------- | ---------------: | ------------------: |
| Ogled           |               Da |                  Da |
| Prenos          |               Da |                  Da |
| Arhiviraj       |               Da |                  Ne |
| Obnovi          |               Ne |                  Da |
| Izbriši         | Glede na pravice |    Glede na pravice |
| Iskanje         |               Da |                  Da |
| Paginacija      |               Da |                  Da |
| Status obdelave |               Da |                  Da |

---

# 21. Pomembna razlika

Vedno si zapomnite:

> **Arhiviranje ne izbriše dokumenta.**

Arhivirani dokument ostane v sistemu in ga je mogoče obnoviti.

> **Brisanje je trajna operacija.**

Pri brisanju se dokument odstrani iz podatkovne zbirke, njegova fizična datoteka pa se odstrani iz shrambe.

---

# 22. Administrator – priporočeni postopek

Pri urejanju dokumentov drugih uporabnikov priporočamo:

1. Preverite dokument.
2. Če ga je treba samo odstraniti iz aktivnega seznama, ga arhivirajte.
3. Če ga je treba ponovno aktivirati, uporabite **Obnovi**.
4. Dokument izbrišite samo, kadar je trajna odstranitev dejansko potrebna.
5. Pri občutljivih dokumentih vedno upoštevajte interne postopke hrambe in varovanja podatkov.
