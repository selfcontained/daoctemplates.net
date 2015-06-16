<?php
namespace Model;

class GameClass {

	private static $options = array(
		1 => 'Armsman',
		2 => 'Cabalist',
		3 => 'Cleric',
		4 => 'Friar',
		5 => 'Heretic',
		6 => 'Infiltrator',
		7 => 'Mercenary',
		8 => 'Minstrel',
		9 => 'Necromancer',
		10 => 'Paladin',
		11 => 'Reaver',
		12 => 'Scout',
		13 => 'Sorcerer',
		14 => 'Theurgist',
		15 => 'Wizard',
		16 => 'Animist',
		17 => 'Bard',
		18 => 'Bainshee',
		19 => 'Blademaster',
		20 => 'Champion',
		21 => 'Druid',
		22 => 'Eldritch',
		23 => 'Enchanter',
		24 => 'Hero',
		25 => 'Mentalist',
		26 => 'Nightshade',
		27 => 'Ranger',
		28 => 'Valewalker',
		29 => 'Vampiir',
		30 => 'Warden',
		31 => 'Berserker',
		32 => 'Bonedancer',
		33 => 'Healer',
		34 => 'Hunter',
		35 => 'Runemaster',
		36 => 'Savage',
		37 => 'Shadowblade',
		38 => 'Shaman',
		39 => 'Skald',
		40 => 'Spiritmaster',
		41 => 'Thane',
		42 => 'Valkyrie',
		43 => 'Warlock',
		44 => 'Warrior',
		45 => 'Mauler-Alb',
		46 => 'Mauler-Hib',
		47 => 'Mauler-Mid'
	);
	
	public static function getOptions() {
		return self::$options;
	}

}