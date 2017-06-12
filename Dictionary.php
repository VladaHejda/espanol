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

}
