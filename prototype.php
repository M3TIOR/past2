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
			body {
				font-family: monospace;
				width: 100%; height: 100%;
				margin: 0px; padding: 0px;
			}

			table {
				background-color: #000;
			}

			td {
				display: inline-block;
				width: 1em;
				height: 1em;
				line-height: 1em;
				font-size: 7px;
				margin: 1px;
				border: 1px solid #FFF;
				text-align: center;
				padding: 0.25em;
				background: #FFF;

				/* disallow text selection */
				-webkit-user-select: none; -webkit-touch-callout: none;
				-khtml-user-select: none; -moz-user-select: none;
				-o-user-select: none; user-select: none;
			}

			.passchar {
				border-radius: 100%;
			}

			@keyframes highlight {
				from { background-color: #000000; }
				50% { background-color: #FF00FF; }
				to { background-color: #FFFFFF; }
			}

			tr:nth-child(even) td:nth-child(odd) {
				background: #DDD;
			}
			tr:nth-child(odd) td:nth-child(even) {
				background: #DDD;
			}

			tr:nth-of-type(1) td {
				background-color: #000 !important;
				color: #FFF !important;
				border: 1px solid white;
			}

			td:nth-of-type(1) {
				background-color: #000 !important;
				color: #FFF !important;
				border: 1px solid white;
			}
		</style>
	</head>
	<body>
		<input id="passarea" type="password" style="width:100%; border:none;"/>
		<div style="display: flex; justify-content: space-around; list-style: none;">
		<table style="flex: 0 1 max-content;" cellspacing="0">
			<?php
				for ($y=0; $y<$gridsize+2; $y++) {
					for ($x=0; $x<$gridsize+2; $x++) {
						if ($x+$y == 0 || $x+$y == $gridsize+2 || $x+$y == ($gridsize+2)*2)

						else if ($y == 0 || $y == $gridsize+2)
							echo "<td scope=\"col\">", str_pad(dechex($x-1), 2, "0", STR_PAD_LEFT), "</td>";
						else if ($x == 0 || $y == $gridsize+2)
							echo "<td scope=\"row\">", str_pad(dechex($y-1), 2, "0", STR_PAD_LEFT), "</td>";
						else echo "<td class=\"passchar\">", htmlentities($chars[$y*$gridsize+1+$x]), "</td>";
					}
				}
			?>
		</table>
		</div>
		<script defer>
			const passarea = document.getElementById("passarea");
			passarea.value="";

			let tracktoggle = false;
			document.addEventListener("mouseup", () => {
				tracktoggle = false;
			});
			document.addEventListener("mousedown", () => {
				tracktoggle = true;
			});

			Array.from(document.getElementsByClassName("passchar")).forEach((e) => {
				e.addEventListener("mousedown", (event) => {
					tracktoggle = true;
					passarea.value += event.target.textContent;
					event.target.style.animation="highlight 3s";
				});
				e.addEventListener("mouseenter", (event) => {
				 	const e = event.target;
					if (tracktoggle && e.style.animationName != "highlight") {
						passarea.value += e.textContent;
						e.style.animation="highlight 3s";
					}
				});
				e.addEventListener("animationend", (event) => {
					event.target.style.animation="unset";
				});
			});
		</script>
	</body>
</html>
