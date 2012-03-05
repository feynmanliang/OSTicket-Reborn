<?
// ctheme language class
// huge class ;) (php4/php5 compatible)

class ctlang {

	// emptyness
	function ctlang() {}

	/*

	Translates a code from the language file and returns it.

	*/
	function dot($string="") {
		global $ctheme_lang;
		if (!$ctheme_lang[$string]) return $string;  // language string wasn't found
		return $ctheme_lang[$string];
	}

	/*

	This is a nasty method, it catches the whole message and asigns a MD5 key, then
	it returns the same string plus the MD5.
	Why? Because osTicket was not designed to be easily translated.
	If a client see's an error he cannot understand, ask him for the MD5 key and
	add it to your language file, so, the next time your client will see the error
	in his own language :P
	If you don't like this feature DO NOT USE IT, I did it for a friend :P

	TODO: save keys somewhere so they can be translate them later? submit a ticket? :P

	*/
	function docatch($string="") {
		if (!$string) return "";  //if nada, return nada :P
		// by default messages from libraries are in english so, if your language
		// was set to english there is no need to catch phrases, unless you want to
		if (CTHEME_LANG == "en" || !CTHEME_LANGC) return $string;
		global $ctheme_lang;
		$cstring = md5($string);
		// check if MD5 key does NOT exists and return them
		if (!$ctheme_lang[$cstring]) return $string."<br>\n[".$cstring."]";
		// if MD5 key is already defined in language file, why not displaying it?
		return $ctheme_lang[$cstring];
	}
}

?>
