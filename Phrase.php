<?php declare(strict_types = 1);

namespace Vlada;

class Phrase
{

	/** @var int */
	private $id;

	/** @var string */
	private $czech;

	/** @var string */
	private $spanish;

	/** @var string|null */
	private $note;

	public function __construct(int $id, string $czech, string $spanish, ?string $note)
	{
		$this->id = $id;
		$this->czech = $czech;
		$this->spanish = $spanish;
		$this->note = $note;
	}

	public function getId(): int
	{
		return $this->id;
	}

	public function getCzech(): string
	{
		return $this->czech;
	}

	public function getSpanish(): string
	{
		return $this->spanish;
	}

	public function getNote(): ?string
	{
		return $this->note;
	}

}
