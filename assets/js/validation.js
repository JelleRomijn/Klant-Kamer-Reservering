document.addEventListener("DOMContentLoaded", function () {
  // Wacht totdat de pagina is geladen
  const form = document.getElementById("reservationForm");

  // Stel de minimale datum in op vandaag
  const dateInput = document.querySelector('input[type="date"]');
  const today = new Date().toISOString().split("T")[0]; // Haal de huidige datum op in YYYY-MM-DD formaat
  dateInput.setAttribute("min", today); // Zorg ervoor dat gebruikers geen datum in het verleden kunnen kiezen

  // Event listener voor als je op verstuur klikt
  form.addEventListener("submit", async function (event) {
    event.preventDefault(); // Voorkom standaard verzending om eerst validatie uit te voeren

    let isValid = true; // het is normaal valid

    // Reset alle errors meldingen bij elke nieuwe validatie
    const errorMessages = document.querySelectorAll(".error-message");
    errorMessages.forEach((el) => (el.textContent = "")); // Maak alle fout meldingen leeg

    // Verwijder de error class van alle invoervelden
    const inputs = form.querySelectorAll("input, select");
    inputs.forEach((input) => input.classList.remove("error"));

    // Valideer de starttijd
    const startTijd = form.querySelector('input[name="start_tijd"]');
    if (!startTijd.value) {
      // Controleer of er een starttijd is ingevuld
      document.getElementById("start-tijd-error").textContent = "Selecteer een starttijd";
      startTijd.classList.add("error"); // Markeer het veld als fout
      isValid = false; // Formulier is niet geldig
    }

    // Valideer de eindtijd
    const eindTijd = form.querySelector('input[name="eind_tijd"]');
    if (!eindTijd.value) {
      // Controleer of er een eindtijd is ingevuld
      document.getElementById("eind-tijd-error").textContent = "Selecteer een eindtijd";
      eindTijd.classList.add("error"); // Markeer het veld als fout
      isValid = false; // Formulier is niet geldig
    } else if (eindTijd.value <= startTijd.value && startTijd.value) {
      // Controleer of de eindtijd na de starttijd ligt
      document.getElementById("eind-tijd-error").textContent = "Eindtijd moet na starttijd zijn";
      eindTijd.classList.add("error"); // Markeer het veld als fout
      isValid = false; // Formulier is niet geldig
    }

    // Valideer de klantnaam
    const klant = form.querySelector('input[name="klant"]');
    if (!klant.value) {
      // Controleer of er een klantnaam is ingevuld
      document.getElementById("klant-error").textContent = "Vul een klantnaam in";
      klant.classList.add("error"); // Markeer het veld als fout
      isValid = false; // Formulier is niet geldig
    } else if (!/^[a-zA-Z0-9\s\-.']+$/.test(klant.value)) {
      // Controleer of de klantnaam alleen toegestane tekens bevat
      document.getElementById("klant-error").textContent = "Klantnaam bevat ongeldige karakters";
      klant.classList.add("error"); // Markeer het veld als fout
      isValid = false; // Formulier is niet geldig
    }

    // Valideer het studentnummer
    const studentNummer = form.querySelector('input[name="student"]');
    if (!studentNummer.value) {
      // Controleer of er een studentnummer is ingevuld
      document.getElementById("student-error").textContent = "Vul een studentnummer in";
      studentNummer.classList.add("error"); // Markeer het veld als fout
      isValid = false; // Formulier is niet geldig
    } else if (!/^\d{6}$/.test(studentNummer.value)) {
      // Controleer of het studentnummer precies 6 cijfers bevat
      document.getElementById("student-error").textContent = "Studentnummer moet uit 6 cijfers bestaan";
      studentNummer.classList.add("error"); // Markeer het veld als fout
      isValid = false; // Formulier is niet geldig
    }

    // Controleer beschikbaarheid in de database als alle tijdvelden geldig zijn
    const lokaal = form.querySelector('select[name="lokaal"]');
    const datum = form.querySelector('input[name="datum"]');

    if (isValid && lokaal.value && datum.value && startTijd.value && eindTijd.value) {
      try {
        // Maak een FormData object om de gegevens te verzenden
        const formData = new FormData();
        formData.append("lokaal", lokaal.value);
        formData.append("datum", datum.value);
        formData.append("start_tijd", startTijd.value);
        formData.append("eind_tijd", eindTijd.value);

        // Stuur een AJAX-verzoek naar de server om beschikbaarheid te controleren
        const response = await fetch("assets/core/check_tijd.php", {
          method: "POST",
          body: formData,
        });

        const data = await response.json();

        if (!data.available) {
          // Er zijn overlappende reserveringen
          isValid = false;

          // Toon een foutmelding met de overlappende tijden
          let overlapMessage = data.message || "Dit lokaal is al gereserveerd tijdens deze tijd:";
          if (Array.isArray(data.overlaps) && data.overlaps.length > 0) {
            overlapMessage = "Dit lokaal is al gereserveerd tijdens deze tijd:";
            data.overlaps.forEach((overlap) => {
              overlapMessage += `\n${overlap.lokaal}: ${overlap.start_tijd} - ${overlap.eind_tijd}`;
            });
          }

          document.getElementById("eind-tijd-error").textContent = overlapMessage;
          eindTijd.classList.add("error");
        }
      } catch (error) {
        console.error("Error checking availability:", error);
      }
    }

    // Als het formulier geldig is, verzend het dan
    if (isValid) {
      form.submit();
    }
  });
});
