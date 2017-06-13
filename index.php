<?php declare(strict_types = 1);

use Vlada\Dictionary;

require_once __DIR__ . '/Dictionary.php';
require_once __DIR__ . '/Phrase.php';

$dictionary = new Dictionary(__DIR__ . '/dictionary.csv');
$fromEsToCz = isset($_GET['es-cz']);

$success = null;
$translatedPhrase = null;
$translation = null;
$help = false;
if (isset($_POST['translation'])) {
	$translatedPhrase = $dictionary->getPhraseById((int) $_POST['phraseId']);
	$help = isset($_POST['help']);

	$word = $fromEsToCz ? $translatedPhrase->getCzech() : $translatedPhrase->getSpanish();
	$translation = $_POST['translation'];

	if (!$help) {
		$giveSomeLove = function (string $word): string {
			$word = mb_strtolower($word, 'utf-8');
			str_replace('ñ', 'ň', $word);
			if (mb_substr($word, 0, 1, 'utf-8') === '¿') {
				$word = mb_substr($word, 1, null, 'utf-8');
			}

			return $word;
		};

		$success = $giveSomeLove($word) === $giveSomeLove($translation);
	}
}

if ($success === null || $success === true) {
	$phrase = $dictionary->getRandomPhrase();
} else {
	$phrase = $translatedPhrase;
}

$word = $fromEsToCz ? $phrase->getSpanish() : $phrase->getCzech();

?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>Español tester (How many spanish do you have in your blood?)</title>
	<style>
		header {
			width: 100%;
			text-align: left;
		}
		h1 {
			width: 100%;
			text-align: center;
			margin-top: 60px;
			font-size: 3em;
		}
		.note {
			width: 100%;
			text-align: center;
		}
		form {
			width: 100%;
			text-align: center;
			margin-top: 30px;
		}
		form input[type=text] {
			font-size: 2em;
			border: black 2px solid;
			padding: 0.1em;
		}
		.translate-submit {
			font-size: 2em;
			border-radius: 0.2em;
		}
		.help {
			width: 100%;
			text-align: center;
			margin-top: 40px;
		}
		.help input {
			font-size: 1em;
			border-radius: 0.2em;
		}
		.result {
			width: 100%;
			text-align: center;
			margin-top: 10px;
			font-size: 1.2em;
		}
		.muy-bien {
			color: darkgreen;
			font-weight: bold;
		}
		.wrong {
			color: darkred;
			font-weight: bold;
		}
	</style>
</head>
<body>

	<header>
		Español tester
		<a href="<?php echo $fromEsToCz ? '?' : '?es-cz'; ?>">prohodit jazyky</a>
	</header>

	<main>
		<?php if ($help) { ?>
			<div class="result">
				<p>
					<i><?php echo $translatedPhrase->getCzech() ?></i>
					=
					<i><?php echo $translatedPhrase->getSpanish() ?></i>
				</p>
			</div>
		<?php } elseif ($success === true) { ?>
			<div class="result">
				<p class="muy-bien">SPRÁVNĚ !</p>
				<p>
					<i><?php echo $translatedPhrase->getCzech() ?></i>
					=
					<i><?php echo $translatedPhrase->getSpanish() ?></i>
				</p>
			</div>
		<?php } elseif ($success === false) { ?>
			<div class="result">
				<p class="wrong">ŠPATNĚ</p>
			</div>
		<?php } ?>

		<h1><?php echo $word; ?></h1>

		<?php if ($phrase->getNote() !== null) { ?>
			<p class="note">
				<?php echo $phrase->getNote(); ?>
			</p>
		<?php } ?>

		<form method="post">
			<label>
				Překlad:
				<input
					type="text"
					name="translation"
					value="<?php if ($success === false) { echo htmlspecialchars($translation); } ?>"
					autocomplete="off"
					autofocus
				>
			</label>
			<input type="hidden" name="phraseId" value="<?php echo $phrase->getId(); ?>">
			<input class="translate-submit" type="submit" value="✔">
			<div class="help">
				<input type="submit" value="nápověda" name="help">
			</div>
		</form>
	</main>

</body>
</html>
