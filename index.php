<?php declare(strict_types = 1);

use Vlada\Dictionary;

require_once __DIR__ . '/Dictionary.php';
require_once __DIR__ . '/Phrase.php';

$dictionary = new Dictionary(__DIR__ . '/dictionary.csv');
$fromEsToCz = isset($_GET['es-cz']);

$success = null;
$translatedPhrase = null;
if (isset($_POST['translation'])) {
	$translatedPhrase = $dictionary->getPhraseById((int) $_POST['phraseId']);

	$word = $fromEsToCz ? $translatedPhrase->getCzech() : $translatedPhrase->getSpanish();
	$translation = $_POST['translation'];

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
	<title>Español tester (How many Spanish do you have in your blood?)</title>
</head>
<body>

	<main>
		<?php if ($success === true) { ?>
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
			<p><?php echo $phrase->getNote(); ?></p>
		<?php } ?>

		<form method="post">
			<label>Překlad: <input type="text" name="translation" autocomplete="off" autofocus></label>
			<input type="hidden" name="phraseId" value="<?php echo $phrase->getId(); ?>">
			<input type="submit" value="Odeslat">
		</form>
	</main>

	<footer>
		Español tester
	</footer>

</body>
</html>
