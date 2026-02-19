<?php

namespace App\Enums;

enum Gender: string
{
	case MALE = 'M';
	case FEMALE = 'W';
	case DIVERSE = 'D';

	/**
	 * Gibt die übersetzte Bezeichnung inkl. Anrede zurück
	 */
	public function label(): string
	{
		return match ($this) {
			self::MALE => __('general.maennlich') . ' (' . __('general.anrede') . ': ' . __('general.herr') . ')',
			self::FEMALE => __('general.weiblich') . ' (' . __('general.anrede') . ': ' . __('general.frau') . ')',
			self::DIVERSE => __('general.divers') . ' (' . __('general.anrede') . ': ' . __('general.ohne') . ')',
		};
	}

	/**
	 * Gibt die Anrede zurück
	 */
	public function salutation(): string
	{
		return match ($this) {
			self::MALE => __('general.herr'),
			self::FEMALE => __('general.frau'),
			self::DIVERSE => '',
		};
	}

	/**
	 * Formatiert die Geschlechter für Selects
	 */
	public static function forSelect(): array
	{
		return collect(self::cases())->map(fn($gender) => [
			'name' => $gender->label(),
			'value' => $gender->value,
		])->toArray();
	}
}
