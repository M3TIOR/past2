<?php
	$chars="1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ~!@#$%^&*()_+{}|:\"<>?`-=[]\\;',./`";
	$gridsize=16;
	$gridcount=($gridsize+1)**2;
	while (strlen($chars) <= $gridcount) $chars=$chars.chr(rand(32,126));
	for ($l=5; $l > 0; $l--) $chars=str_shuffle($chars);

	// TODO: maybe implement row and column single click secions with the following?
	//       I could just do it using Javascript but raw HTML is better.
	// https://stackoverflow.com/questions/35738049/select-only-a-single-column-in-a-html-table
?>
<html>
	<head>
		<style>
			* { font-size: 2.5vmin; }
			button { width: 50%; }
			.passchar { border-radius: 100%; overflow: hidden; }
			/* Use unicode to cleverly hide the cipher from outside observer's FOV */
			.passchar::before { content: "\2022\0A_\0A"; }
			.cipher-show .passchar::before { content: ""; }

			body {
				font-family: monospace;
				width: 100%; height: 100%;
				margin: 0px; padding: 0px;
			}

			[type=checkbox] {
				border: 1px solid; border-radius: 1em;
				height: 1em; width: 1em;
				/* Fixes: https://bugzilla.mozilla.org/show_bug.cgi?id=605985 */
				-moz-appearance: none;
				/* Fixes a placement issue */
				-moz-transform: translateY(.33em);
			}

			td {
				display: inline-block;
				width: 3vmin;
				height: 3vmin;
				line-height: 1em;
				margin: 1px;
				border: 1px solid;
				text-align: center;
				padding: 0.25vmin;

				/* disallow text selection */
				-webkit-user-select: none; -webkit-touch-callout: none;
				-khtml-user-select: none; -moz-user-select: none;
				-o-user-select: none; user-select: none;
			}

			/************************** Theming Begin *******************************/
			* {
				/* 60, 30, 10 rule */
				--primary-color: #000;
				--secondary-color: #1f1f1f;
				/* --primary-color: #130027;
				--secondary-color: #230445; */
				--accent-1-color: #23094e;
				--accent-2-color: #4c1a81;
				--accent-3-color: #8f37cb;

				color: var(--accent-3-color);
			}

			@media (prefers-color-scheme: dark) {

			}

			@media (prefers-color-scheme: light) {
				* {
					--primary-color: #fafafc;
					--secondary-color: #ddd;
					--accent-3-color: #113a6a;
					--accent-1-color: #224aaa;
					--accent-2-color: #335aea;
				}
			}

			body { background: var(--primary-color); }
			button { background: var(--secondary-color); border-color: var(--accent-3-color); }
			input { background: var(--primary-color); border-color: var(--accent-1-color); }
			button:hover > [type=checkbox], [type=checkbox]:hover { background: var(--accent-1-color); }
			button:hover > [type=checkbox]:checked, [type=checkbox]:checked:hover { background: var(--accent-2-color); }
			[type=checkbox]:checked { background: var(--accent-3-color); }
			table { background: var(--primary-color); }
			td { background: var(--secondary-color); border-color: var(--accent-2-color); }
			::selection { background: var(--accent-1-color); }
			/* tr:nth-child(even) td:nth-child(odd) { background: var(--accent-1-color); }
			tr:nth-child(odd) td:nth-child(even) { background: var(--accent-1-color); } */

			/* tr:nth-of-type(1) td, td:nth-of-type(1) {
				background: var(--primary-color) !important;
				border: 1px solid var(--accent-2-color);
			} */

			@keyframes draw {
				from { background-color: var(--accent-2-color); }
				to { background-color: var(--accent-1-color); }
			}
		</style>
	</head>
	<body style="display: flex; flex-direction: column">
		<input style="flex: 1; width: 100%;" id="passarea" type="password"/>
		<div style="flex: 1; display: flex; justify-content: space-around; flex-wrap: wrap;">
			<button>Show Password: <input id="showpass" type="checkbox"/></button>
			<button>Show Cipher: <input id="showciph" type="checkbox"/></button>
		</div>
		<div style="flex: 10; display: flex; justify-content: space-around; padding: 2.5vmin">
			<table id="cipherin" style="aspect-ratio: 1 / 1; width: max-content; height: max-content; margin: auto;" cellspacing="0">
				<?php
					for ($y=0; $y<$gridsize+1; $y++) {
						echo "<tr>";
						for ($x=0; $x<$gridsize+1; $x++) {
							if ($x+$y == 0) echo "<td> </td>";
							else if ($y == 0) echo "<td scope=\"col\">", str_pad(dechex($x-1), 2, "0", STR_PAD_LEFT), "</td>";
							else if ($x == 0) echo "<td scope=\"row\">", str_pad(dechex($y-1), 2, "0", STR_PAD_LEFT), "</td>";
							else echo "<td class=\"passchar\">", htmlentities($chars[$y*$gridsize+1+$x]), "</td>";
						}
						echo "</tr>";
					}
				?>
			</table>
		</div>
		<div style="flex: 1; display: flex; justify-content: space-around;  width: 100%">
			<button id="clipcopy">Copy to Clipboard</button>
			<button id="clearpat">Clear Pattern</button>
		</div>
		<script defer>
			const passarea = document.getElementById("passarea");
			const cipherin = document.getElementById("cipherin");
			const showpass = document.getElementById("showpass");
			const showciph = document.getElementById("showciph");
			const clipcopy = document.getElementById("clipcopy");
			const clearpat = document.getElementById("clearpat");

			// Ensure autofill doesn't compromise security.
			passarea.value="";
			showciph.checked = showpass.checked = false;

			// Join nested checkbox logic with parent buttons.
			showpass.parentElement.addEventListener("click", ()=>showpass.click());
			showciph.parentElement.addEventListener("click", ()=>showciph.click());

			// Toggle password visibility
			showpass.addEventListener("change", ()=>{
				passarea.type = (showpass.checked ? "text" : "password");
			});

			showciph.addEventListener("change", ()=>{
				if (showciph.checked === true)
					cipherin.classList.add("cipher-show");
				else
					cipherin.classList.remove("cipher-show");
			});

			clipcopy.addEventListener("click", ()=>{
				passarea.select();
				passarea.setSelectionRange(0, 99999); /* For mobile devices */
				navigator.clipboard.writeText(passarea.value).catch(()=>{
					alert("There was a problem with the clipboard API in this browser.");
				});
			});

			clearpat.addEventListener("click", ()=>passarea.value='')

			let tracktoggle = false;
			document.addEventListener("mouseup", () => { tracktoggle = false; });
			document.addEventListener("mousedown", () => { tracktoggle = true; });

			Array.from(document.getElementsByClassName("passchar")).forEach((e) => {
				e.addEventListener("mousedown", (event) => {
					tracktoggle = true;
					passarea.value += event.target.textContent;
					event.target.style.animation="draw 3s";
				});
				e.addEventListener("mouseenter", (event) => {
				 	const e = event.target;
					if (tracktoggle && e.style.animationName !== "draw") {
						passarea.value += e.textContent;
						e.style.animation="draw 3s";
					}
				});
				e.addEventListener("animationend", (event) => {
					event.target.style.animation="unset";
				});
			});
		</script>
	</body>
</html>
