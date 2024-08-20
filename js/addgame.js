"use strict";

window.addEventListener("DOMContentLoaded", () => {
	const playerSelections = document.getElementsByClassName("playerSelect");
	const ownerSelections = document.getElementsByClassName("ownerSelect");

	for (let pSelect of playerSelections) {
		pSelect.addEventListener("change", (ev) => {
			const oSelect = ev.target.parentElement.nextElementSibling.firstElementChild.nextElementSibling;
			oSelect.selectedIndex = ev.target.selectedIndex;
			oSelect.dispatchEvent(new Event("change"));
		});
	}

	for (let oSelect of ownerSelections) {
		const dSelect = oSelect.parentElement.nextElementSibling.firstElementChild.nextElementSibling;
		oSelect.addEventListener("change", (ev) => {
			if (oSelect.value != "") {
				const getDeckList = new XMLHttpRequest();
				getDeckList.open("get", `/php/async/get_decks.php?id=${encodeURI(oSelect.value)}`);
				getDeckList.addEventListener("load", (ev) => {
					if (getDeckList.status == 200) {
						const decks = getDeckList.response.split("/");
						dSelect.innerHTML = "";
						dSelect.options[0] = new Option();
						for (let deck of decks) {
							if (deck.length > 0) {
								let data = deck.split("\\");
								dSelect.options[dSelect.options.length] = new Option(he.decode(data[1]), data[0]);
							}
						}
					}
					else {
						console.log(`getDeckList xhr error`);
					}
				});
				getDeckList.send();
			}
			else {
				dSelect.innerHTML = "";
			}
		});
	}
});