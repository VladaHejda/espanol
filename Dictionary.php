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
			list ($czech, $spanish, $note) = explode(';', $row);
			$czech = trim($czech);
			$spanish = trim($spanish);
			$note = trim($note);

			$this->phrases[$id] = new Phrase($id, $czech, $spanish, $note !== '' ? $note : null);
			$id++;
		}
	}

	/**
	 * @param int|null $lastId
	 * @param int[] $excludeIds
	 * @return Phrase
	 */
	public function getRandomPhrase(int $lastId = null, array $excludeIds = []): Phrase
	{
		if (count($excludeIds) === count($this->phrases)) {
			throw new EverythingTranslatedException();
		}

		if ($lastId !== null) {
			$excludeIds[] = $lastId;
		}

		$sourcePhrases = array_values(array_diff(array_keys($this->phrases), $excludeIds));
		if (count($sourcePhrases) === 0) {
			return $this->phrases[$lastId];
		}

		$randomId = $sourcePhrases[mt_rand(0, count($sourcePhrases) - 1)];

		return $this->phrases[$randomId];
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
