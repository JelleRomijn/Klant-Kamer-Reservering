# Klant-Kamer-Reservering
## TODO:
- change the grid to FR units so you can add more rows and it will just scale also make it so they just dont have to be induviduially put into css (Have the minimum amount of rows as X amount)
- If too much reserveringen, make it auto scroll thru whole list
- Make it so login checks a list and if it matches it sets a value to logged in
- Make login admin the one for docenten
- Add password shit for docenten
- Make it so CMS can only be accesed if ur logged in
- Add credits to [Jesse Hardorff](https://github.com/JesseHardorff) en [Tristan van Buuren](https://github.com/TristanvanBuuren)

## INFO:
**[Google Drive](https://drive.google.com/drive/folders/1Z6lwA7q-LwSepJPliSu6eTiFBDLbfvcP?usp=sharing)** <br>
**[Notion](https://www.notion.so/het-bureau/Ruimte-Reservering-Systeem-b6e3211aff794526a618e8a89bd8f6f5?source=copy_link)** <br>
**[Github](https://github.com/JesseHardorff/Klant-Kamer-Reservering/)** <br>
**Questions** <br>
    For questions contact [Tristan van Buuren](https://github.com/TristanvanBuuren)

## Pages
**index.php** - Deze pagina is de start pagina, hier word de reserveringen van vandaag en in de toekomst laten zien, De QR code verwijst je door naar *reserve.php*. Formaten in deze pagina zijn 1080x1920 en 1920x1080 maar moet ook voor telefoon en andere scherm formaten beschikbaar worden. <br>
**lijst.php** - Deze pagina is voor docenten (niet zichtbaar in de frontend voor klanten), Als edit enabled is kan je dubbel clicken op en veld en deze bewerken, Je kan ook de filters gebruiken, De search bar filtered op Datum, Tijd, Lokaal, Gepland door, Klant, Type. Formaten zijn Meeste computer horizontale schermen. <br>
**login-admin.php** - Deze pagina is de log in pagina voor docenten. Formaten zijn meeste telefoon schermen maar moet ook een computer pagina worden. <br>
**login.php** - Deze pagina is voor studenten. Na inloggen word je direct doorgestuurd naar *reserve.php*. Formaten zouden alle moeten worden maar is het niet <br>
**reserve.php** - Deze pagina kan je reserveringen aanmaken, je hebt meerdere velden. Formaten zijn telefoon (werkt ook wel op computers maar niet mooi) en moet alles worden. <br>
**verstuurd.php** - Deze pagina word getoont als je hebt gereserveerd en dit succesvol/onsuccessvol is. Formaten zijn telefoon (werkt ook wel op computers maar niet mooi) en moet alles worden. <br>