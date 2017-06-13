<?php declare(strict_types = 1);

namespace Vlada;

class Dictionary
{

	/** @var Phrase[] */
	private $phrases;

	public function __construct(string $dictionaryFile)
	{
		$id = 1;
		foreach (file($dictionaryFile) as $row) {
			[$czech, $spanish, $note] = explode(';', $row);
			$czech = trim($czech);
			$spanish = trim($spanish);
			$note = trim($note);

			$this->phrases[$id] = new Phrase($id, $czech, $spanish, $note !== '' ? $note : null);
			$id++;
		}
	}

	public function getRandomPhrase(): Phrase
	{
		return $this->phrases[array_rand($this->phrases)];
	}

	public function getPhraseById(int $id): Phrase
	{
		return $this->phrases[$id];
	}

	public function isTranslationFromEsToCzRight(Phrase $phrase, string $translation, bool $strict = false): bool
	{
		return $this->isTranslationRight($phrase, $translation, true, $strict);
	}

	public function isTranslationFromCzToEsRight(Phrase $phrase, string $translation, bool $strict = false): bool
	{
		return $this->isTranslationRight($phrase, $translation, false, $strict);
	}

	private function isTranslationRight(Phrase $phrase, string $translation, bool $fromEsToCz, bool $strict): bool
	{
		$word = $fromEsToCz ? $phrase->getCzech() : $phrase->getSpanish();

		if ($strict) {
			return $word === $translation;
		}

		$giveSomeLove = function (string $word): string {
			$word = mb_strtolower($word, 'utf-8');
			$word = str_replace('ñ', 'ň', $word);
			if (mb_substr($word, 0, 1, 'utf-8') === '¿') {
				$word = mb_substr($word, 1, null, 'utf-8');
			}

			return trim($word);
		};

		return $giveSomeLove($word) === $giveSomeLove($translation);
	}

}
