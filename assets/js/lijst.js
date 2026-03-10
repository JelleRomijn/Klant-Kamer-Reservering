document.addEventListener("DOMContentLoaded", function () {
  // Variabelen
  const tabel = document.getElementById("reserveringen-tabel");
  const deleteModal = document.getElementById("delete-modal");
  const confirmDeleteBtn = document.getElementById("confirm-delete");
  const cancelDeleteBtn = document.getElementById("cancel-delete");
  const lokaalFilter = document.getElementById("lokaal-filter");
  const datumFilter = document.getElementById("datum-filter");
  const zoekFilter = document.getElementById("zoek-filter");

  let currentEditRow = null;
  let deleteReservationId = null;

  // Event listeners voor filters
  lokaalFilter.addEventListener("change", filterTable);
  datumFilter.addEventListener("change", filterTable);
  zoekFilter.addEventListener("input", filterTable);

  // Event delegation voor edit en delete knoppen
  tabel.addEventListener("click", function (e) {
    // Edit knop
    if (e.target.classList.contains("item-edit")) {
      handleEditClick(e.target);
    }

    // Delete knop
    if (e.target.classList.contains("item-delete")) {
      const row = e.target.closest("tr");
      deleteReservationId = row.dataset.id;
      deleteModal.style.display = "block";
    }
  });

  // Event listeners voor delete modal
  confirmDeleteBtn.addEventListener("click", function () {
    if (deleteReservationId) {
      deleteReservation(deleteReservationId);
    }
  });

  cancelDeleteBtn.addEventListener("click", function () {
    deleteModal.style.display = "none";
    deleteReservationId = null;
  });

  // Functie om een rij bewerkbaar te maken
  // Functie om een rij bewerkbaar te maken
  function handleEditClick(button) {
    const row = button.closest("tr");

    // Als we al bezig zijn met bewerken van deze rij
    if (button.classList.contains("active")) {
      // Sla wijzigingen op
      saveChanges(row);
      button.classList.remove("active");
      button.textContent = "EDIT";
      currentEditRow = null;

      // Verwijder alle input velden
      row.querySelectorAll(".editable.editing").forEach((cell) => {
        const input = cell.querySelector("input");
        const select = cell.querySelector("select");

        if (input) {
          cell.textContent = input.value;
        } else if (select) {
          cell.textContent = select.value;
        }

        cell.classList.remove("editing");
      });
    } else {
      // Als er al een andere rij in edit-modus is, sla die wijzigingen eerst op
      if (currentEditRow !== null) {
        const activeButton = currentEditRow.querySelector(".item-edit.active");
        if (activeButton) {
          // Simuleer een klik op de actieve edit-knop om wijzigingen op te slaan
          activeButton.click();
        }
      }

      // Maak deze rij bewerkbaar
      button.classList.add("active");
      button.textContent = "OPSLAAN";
      currentEditRow = row;
      makeRowEditable(row);
    }
  }

  // Zet alle bewerkbare cellen van een rij direct in edit-modus
  function makeRowEditable(row) {
    row.querySelectorAll(".editable").forEach((cell) => {
      makeEditable(cell);
    });
  }

  // Functie om een cel bewerkbaar te maken
  function makeEditable(cell) {
    if (!cell.classList.contains("editing")) {
      const value = cell.textContent;
      cell.textContent = "";
      cell.classList.add("editing");

      // Als het veld 'lokaal' is, maak dan een select box
      if (cell.dataset.field === "lokaal") {
        const select = document.createElement("select");
        select.className = "edit-select";

        // Voeg opties toe
        const lokalen = ["W002a", "W002b", "W002", "W003a", "W003b", "W003"];

        // Voeg een lege optie toe
        const defaultOption = document.createElement("option");
        defaultOption.value = "";
        defaultOption.textContent = "Selecteer lokaal";
        defaultOption.disabled = true;
        select.appendChild(defaultOption);

        // Voeg alle lokaalopties toe
        lokalen.forEach((lokaal) => {
          const option = document.createElement("option");
          option.value = lokaal;
          option.textContent = lokaal;
          if (lokaal === value) {
            option.selected = true;
          }
          select.appendChild(option);
        });

        cell.appendChild(select);
        select.focus();

        // Select blijft actief tot gebruiker op OPSLAAN klikt.
      }
      // Als het veld 'type' is, maak dan een select box
      else if (cell.dataset.field === "type") {
        const select = document.createElement("select");
        select.className = "edit-select";

        // Voeg opties toe
        const types = ["Klant gesprek", "Team vergadering", "Workshop"];

        // Voeg een lege optie toe
        const defaultOption = document.createElement("option");
        defaultOption.value = "";
        defaultOption.textContent = "Selecteer type";
        defaultOption.disabled = true;
        select.appendChild(defaultOption);

        // Voeg alle type-opties toe
        types.forEach((type) => {
          const option = document.createElement("option");
          option.value = type;
          option.textContent = type;
          if (type === value) {
            option.selected = true;
          }
          select.appendChild(option);
        });

        cell.appendChild(select);
        select.focus();

        // Select blijft actief tot gebruiker op OPSLAAN klikt.
      } else {
        // Voor andere velden, gebruik een input zoals voorheen
        const input = document.createElement("input");
        input.type = "text";
        input.value = value;

        // Speciale types voor bepaalde velden
        if (cell.dataset.field === "datum") {
          input.type = "date";
          // Converteer dd-mm-yyyy naar yyyy-mm-dd voor date input
          const parts = value.split("-");
          if (parts.length === 3) {
            input.value = `${parts[2]}-${parts[1]}-${parts[0]}`;
          }
        } else if (cell.dataset.field === "start_tijd" || cell.dataset.field === "eind_tijd") {
          input.type = "time";
        }

        cell.appendChild(input);
        input.focus();

        // Enter slaat direct op voor snelle workflow.
        input.addEventListener("keydown", function (e) {
          if (e.key === "Enter") {
            saveChanges(cell.closest("tr"));
            cell.textContent = input.value;
            cell.classList.remove("editing");

            const row = cell.closest("tr");
            const editButton = row ? row.querySelector(".item-edit.active") : null;
            if (editButton) {
              editButton.classList.remove("active");
              editButton.textContent = "EDIT";
            }
            currentEditRow = null;
          }
        });
      }
    }
  }

  // Functie om wijzigingen op te slaan
  // Functie om wijzigingen op te slaan
  function saveChanges(row) {
    const id = row.dataset.id;
    const data = {};

    // Verzamel alle gewijzigde gegevens
    row.querySelectorAll(".editable.editing").forEach((cell) => {
      const field = cell.dataset.field;

      // Controleer of het een select of input element is
      let value;
      if (cell.dataset.field === "lokaal" || cell.dataset.field === "type") {
        const select = cell.querySelector("select");
        value = select ? select.value : "";
        data[field] = value;
      } else {
        const input = cell.querySelector("input");
        value = input ? input.value : "";

        // Converteer datum van yyyy-mm-dd naar dd-mm-yyyy voor weergave
        if (field === "datum" && input && input.type === "date") {
          const date = new Date(value);
          if (!isNaN(date.getTime())) {
            const day = String(date.getDate()).padStart(2, "0");
            const month = String(date.getMonth() + 1).padStart(2, "0");
            const year = date.getFullYear();
            value = `${day}-${month}-${year}`;

            // Voor database hebben we yyyy-mm-dd nodig
            data[field] = `${year}-${month}-${day}`;
          }
        } else {
          data[field] = value;
        }
      }

      // Update de celinhoud
      cell.textContent = value;
      cell.classList.remove("editing");
    });

    // Stuur gegevens naar server als er wijzigingen zijn
    if (Object.keys(data).length > 0) {
      updateReservation(id, data);
    }
  }

  // Functie om reservering bij te werken via AJAX
  function updateReservation(id, data) {
    fetch("assets/core/update_reservation.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        id: id,
        data: data,
      }),
    })
      .then((response) => response.json())
      .then((result) => {
        if (result.success) {
        } else {
          alert("Fout bij bijwerken: " + result.message);
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        alert("Er is een fout opgetreden bij het bijwerken van de reservering.");
      });
  }

  // Functie om reservering te verwijderen via AJAX
  function deleteReservation(id) {
    fetch("assets/core/delete_reservation.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        id: id,
      }),
    })
      .then((response) => response.json())
      .then((result) => {
        if (result.success) {
          // Verwijder de rij uit de tabel
          const row = document.querySelector(`tr[data-id="${id}"]`);
          if (row) {
            row.remove();
          }
        } else {
          alert("Fout bij verwijderen: " + result.message);
        }
        deleteModal.style.display = "none";
      })
      .catch((error) => {
        console.error("Error:", error);
        alert("Er is een fout opgetreden bij het verwijderen van de reservering.");
        deleteModal.style.display = "none";
      });
  }

  // Functie om tabel te filteren
  function filterTable() {
    const lokaal = lokaalFilter.value.toLowerCase();
    const datum = datumFilter.value;
    const zoekterm = zoekFilter.value.toLowerCase();

    const rows = tabel.querySelectorAll("tr.table-item");

    rows.forEach((row) => {
      const lokaalCell = row.querySelector('td[data-field="lokaal"]');
      const datumCell = row.querySelector('td[data-field="datum"]');
      const rowText = row.textContent.toLowerCase();

      let toonRij = true;

      // Filter op lokaal
      if (lokaal !== "all" && lokaalCell) {
        const lokaalValue = lokaalCell.textContent.toLowerCase();
        if (!lokaalValue.includes(lokaal)) {
          toonRij = false;
        }
      }

      // Filter op datum
      if (datum && datumCell) {
        // Converteer dd-mm-yyyy naar yyyy-mm-dd voor vergelijking
        const parts = datumCell.textContent.split("-");
        if (parts.length === 3) {
          const formattedDate = `${parts[2]}-${parts[1]}-${parts[0]}`;
          if (formattedDate !== datum) {
            toonRij = false;
          }
        }
      }

      // Filter op zoekterm
      if (zoekterm && !rowText.includes(zoekterm)) {
        toonRij = false;
      }

      row.style.display = toonRij ? "" : "none";
    });
  }
});
