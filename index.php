<?php declare(strict_types = 1);

use Vlada\Dictionary;
use Vlada\EverythingTranslatedException;

require_once __DIR__ . '/Dictionary.php';
require_once __DIR__ . '/Phrase.php';
require_once __DIR__ . '/EverythingTranslatedException.php';

$dictionary = new Dictionary(__DIR__ . '/dictionary.csv');
$fromEsToCz = isset($_GET['es-cz']);

session_start();
if (!isset($_SESSION['translatedIds'])) {
	$_SESSION['translatedIds'] = [];
}

$success = null;
$translatedPhrase = null;
$translation = null;
$help = false;
$congrats = false;

if (isset($_POST['translation'])) {
	$translatedPhrase = $dictionary->getPhraseById((int) $_POST['phraseId']);
	$help = isset($_POST['help']);

	if (!$help) {
		$translation = $_POST['translation'];

		$success = $fromEsToCz
			? $dictionary->isTranslationFromEsToCzRight($translatedPhrase, $translation, false)
			: $dictionary->isTranslationFromCzToEsRight($translatedPhrase, $translation, false);

		if ($success) {
			$_SESSION['translatedIds'][$translatedPhrase->getId()] = true;
		}
	}
}

if ($success === null || $success === true) {
	try {
		$phrase = $dictionary->getRandomPhrase(
			$translatedPhrase !== null ? $translatedPhrase->getId() : null,
			array_keys($_SESSION['translatedIds'])
		);
	} catch (EverythingTranslatedException $e) {
		$_SESSION['translatedIds'] = [];
		$phrase = $dictionary->getRandomPhrase($translatedPhrase->getId());
		$congrats = true;
	}
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
		.congrats {
			color: darkviolet;
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
					<?php if ($translatedPhrase->getNote() !== null) { ?>(<?php echo $translatedPhrase->getNote() ?>)<?php } ?>
				</p>
			</div>
		<?php } elseif ($success === true) { ?>
			<div class="result">
				<p class="muy-bien">SPRÁVNĚ !</p>
				<p>
					<i><?php echo $translatedPhrase->getCzech() ?></i>
					=
					<i><?php echo $translatedPhrase->getSpanish() ?></i>
					<?php if ($translatedPhrase->getNote() !== null) { ?>(<?php echo $translatedPhrase->getNote() ?>)<?php } ?>
				</p>
			</div>

			<?php if ($congrats) { ?>
				<div class="result">
					<p class="congrats">Dnes jsi přeložil již všechna slovíčka. Gratuluji!</p>
				</div>
			<?php } ?>

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
