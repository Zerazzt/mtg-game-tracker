"use strict";

window.addEventListener("DOMContentLoaded", () => {
	const openButtons = document.getElementsByClassName("opener");
	const addModal = document.getElementById("add");

	for (let btn of openButtons) {
		btn.addEventListener("click", (ev) => {
			ev.preventDefault();
			// addModal.style.display = "block";
			addModal.show();
			const getFormElements = new XMLHttpRequest();
			getFormElements.open("get", `./php/async/${btn.name}.php`);
			getFormElements.addEventListener("load", (ev) => {
				if (getFormElements.status == 200) {
					addModal.innerHTML = getFormElements.response;
					addModal.firstElementChild.nextElementSibling.classList.add(btn.name);
				}
				else {
					console.log(`getFormElements xhr error`);
				}
			});
			getFormElements.send();

			const playerSelections = document.getElementsByClassName("playerSelect");
			const ownerSelections = document.getElementsByClassName("ownerSelect");

			const getPlayerList = new XMLHttpRequest();
			getPlayerList.open("get", `php/async/get_players.php`);
			getPlayerList.addEventListener("load", (ev) => {
				if (getPlayerList.status == 200) {
					const players = getPlayerList.response.split("/");
					for (let pSelect of playerSelections) {
						pSelect.innerHTML = "";
						pSelect.options[0] = new Option();
						for (let player of players) {
							if (player.length > 0) {
								let data = player.split("\\");
								pSelect.options[pSelect.options.length] = new Option(data[1], data[0]);
							}
						}
						pSelect.addEventListener("change", (ev) => {
							const oSelect = ev.target.parentElement.nextElementSibling.firstElementChild.nextElementSibling;
							oSelect.selectedIndex = ev.target.selectedIndex;
							oSelect.dispatchEvent(new Event("change"));
						});
					}
					for (let oSelect of ownerSelections) {
						const dSelect = oSelect.parentElement.nextElementSibling.firstElementChild.nextElementSibling;
						oSelect.innerHTML = "";
						oSelect.options[0] = new Option();
						for (let player of players) {
							if (player.length > 0) {
								let data = player.split("\\");
								oSelect.options[oSelect.options.length] = new Option(data[1], data[0]);
							}
						}
						oSelect.addEventListener("change", (ev) => {
							if (oSelect.value != "" && addModal.firstElementChild.nextElementSibling.classList.contains("game")) {
								const getDeckList = new XMLHttpRequest();
								getDeckList.open("get", `php/async/get_decks.php?id=${encodeURI(oSelect.value)}`);
								getDeckList.addEventListener("load", (ev) => {
									if (getDeckList.status == 200) {
										const decks = getDeckList.response.split("/");
										dSelect.innerHTML = "";
										dSelect.options[0] = new Option();
										for (let deck of decks) {
											if (deck.length > 0) {
												let data = deck.split("\\");
												dSelect.options[dSelect.options.length] = new Option(data[1], data[0]);
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
				}
				else {
					console.log(`getPlayerList xhr error`);
				}
			});
			getPlayerList.send();
		});
	}

	window.onclick = (ev) => {
		if (ev.target == addModal) {
			// addModal.style.display = "none";
			addModal.close();
			
			addModal.firstElementChild.firstElementChild.nextElementSibling.classList.remove(["user", "deck", "game"]);
		}
	}
});