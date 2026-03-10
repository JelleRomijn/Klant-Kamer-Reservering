# Registratie Implementatie - Installatiehandleiding

## Wat is er veranderd?

De registratiefunctionaliteit is volledig geïmplementeerd. Gebruikers kunnen nu:
1. **Registreren** met hun student nummer, voornaam, achternaam, e-mailadres en wachtwoord
2. **Inloggen** met e-mailadres of student nummer
3. Gegevens worden opgeslagen in de database

## Stappen om te implementeren:

### 1. Database Schema Bijwerken

Open phpMyAdmin en importeer de **bijgewerkte** `bureau_kamer.sql` bestand, OF voer handmatig deze SQL-commando's uit:

```sql
-- Nieuwe users tabel
CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `student_nummer` int(6) NOT NULL UNIQUE,
  `voornaam` varchar(255) NOT NULL,
  `achternaam` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `gemaakt_op` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `student_nummer` (`student_nummer`);

ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
```

### 2. Bestanden gecontroleerd

De volgende bestanden zijn geüpdatet:
- ✅ `/register.php` - Volledig implementeerde registratieformulier en backend logica
- ✅ `/login.php` - Bijgewerkt voor wachtwoord-verificatie
- ✅ `/bureau_kamer.sql` - Bijgewerkt met users tabel
- ✅ `/assets/css/register.css` - Styling voor registratiepagina
- ✅ `/assets/css/login.css` - Geüpdatet met error messaging

## Functies:

### Registratie (`register.php`)
- ✅ Valideert alle invoervelden
- ✅ Controleert of student nummer of e-mail al bestaat
- ✅ Wachtwoord moet minimaal 6 karakters zijn
- ✅ Wachtwoorden moeten overeenkomen
- ✅ Wachtwoord wordt gehasht met bcrypt
- ✅ Succesbericht na registratie
- ✅ Link naar inlogpagina

### Login (`login.php`)
- ✅ Accepteert e-mailadres of student nummer
- ✅ Verifiëert wachtwoord veilig
- ✅ Stelt sessievariabelen in
- ✅ Foutmeldingen voor verkeerde inloggegevens
- ✅ Link naar registratiepagina

## Gebruikersflow:

1. Bezoeker gaat naar **register.php**
2. Vult formulier in (student nummer, voornaam, achternaam, e-mail, wachtwoord)
3. Klikt "Registreren"
4. Gegevens worden opgeslagen in de `users` tabel
5. Ontvangt bevestigingsbericht
6. Gaat naar **login.php** om in te loggen
7. Voert e-mail of student nummer en wachtwoord in
8. Wordt doorgestuurd naar **reserve.php** na succesvolle login

## Testen:

1. Open http://localhost/Klant-Kamer-Reservering/register.php
2. Vul het formulier in
3. Verifieer dat de gegevens in de database opgeslagen zijn
4. Test inloggen op login.php

## Alleen registreren op locatie (Wi‑Fi)

Registratie is beperkt tot clients die verbonden zijn met het netwerk van het gebouw. De server controleert het IP-adres van de bezoeker tegen een lijst van toegestane subnetten (CIDR) die zijn ingesteld in `assets/core/config.php`.

- Om de toegestane netwerken aan te passen, open `assets/core/config.php` en wijzig de array `$ALLOWED_SUBNETS`.
- Voorbeeldwaarden (aan te passen naar jouw omgeving): `192.168.1.0/24`, `10.0.0.0/8`, `172.16.0.0/12`.
- De controle gebruikt de header `HTTP_X_FORWARDED_FOR` indien aanwezig, anders `REMOTE_ADDR`.

Als je een andere methode wilt (bijv. captive portal of SSID-detectie), laat het weten — dat vereist netwerk-/infrastructuurconfiguratie buiten deze PHP-app.

Klaar! 🎉
