<?php

namespace Mepatek\Components\International;

use Nette\Caching\Cache;
use Nette\Caching\IStorage;

class LanguageHelper
{

	/** @var Cache */
	private $cache;
	/** @var string */
	private $possibleLanguages;

	/**
	 * LanguageFactory constructor.
	 *
	 * @param string   $possibleLanguages comma separated list of possible languages, null = all
	 * @param IStorage $storage
	 */
	public function __construct($possibleLanguages = null, IStorage $storage)
	{
		$this->possibleLanguages = $possibleLanguages;
		$this->cache = new Cache($storage, "languageFactory");
	}

	/**
	 * @param string|null $selectedLanguage Selected language
	 *
	 * @return array
	 */
	public function getSelectItems($selectedLanguage = null)
	{
		$selectItems = [];
		$languages = $this->getLanguages();
		foreach ($languages as $language) {
			$selectItems[$language->id] = $language->name;
		}
		if ($selectedLanguage and !array_key_exists($selectedLanguage, $selectItems)) {
			$selectItems[$selectedLanguage] = $selectedLanguage;
		}
		return $selectItems;
	}

	/**
	 * @return Language[]
	 */
	public function getLanguages()
	{
		return $this->cache->load(
			"languages", function () {
			$languages = [];

			$possibleLanguages = $this->possibleLanguages ? preg_split("~,\s*~", $this->possibleLanguages) : null;
			$langRows = explode("\n", self::LANGUAGE_ISO_639_1);
			foreach ($langRows as $langRow) {
				$language = explode("\t", $langRow);
				if ($possibleLanguages == null or in_array($language[0], $possibleLanguages)) {
					$languages[] = new Language($language);
				}
			}
			return $languages;
		}
		);
	}

	const LANGUAGE_ISO_639_1 = <<< EOT
aa	afarština	Afaraf	Afar
ab	abcházština	Аҧсуа	Abkhaz
ae	avestánština	avesta	Avestan
af	afrikánština	Afrikaans	Afrikaans
ak	akanština	Akan	Akan
am	amharština	አማርኛ	Amharic
an	aragonština	Aragonés	Aragonese
ar	arabština	‫العربية‬	Arabic
as	ásámština	অসমীয়া	Assamese
av	avarština	авар мацӀ; магӀарул мацӀ	Avaric
ay	ajmarština	aymar aru	Aymara
az	ázerbájdžánština	azərbaycan dili	Azerbaijani
ba	baškirština	башҡорт теле	Bashkir
be	běloruština	Беларуская	Belarusian
bg	bulharština	български език	Bulgarian
bh	bihárština	भोजपुरी	Bihari
bi	bislamština	Bislama	Bislama
bm	bambarština	bamanankan	Bambara
bn	bengálština	বাংলা	Bengali
bo	tibetština	བོད་ཡིག	Tibetan
br	bretonština	brezhoneg	Breton
bs	bosenština	bosanski jezik	Bosnian
ca	katalánština	Català	Catalan
ce	čečenština	нохчийн мотт	Chechen
ch	chamorro	Chamoru	Chamorro
co	korsičtina	corsu; lingua corsa	Corsican
cr	kríjština	ᓀᐦᐃᔭᐍᐏᐣ	Cree
cs	čeština	čeština	Czech
cu	staroslověnština	словѣньскъ ѩꙁꙑкъ	Church Slavic
cv	čuvaština	чӑваш чӗлхи	Chuvash
cy	velština	Cymraeg	Welsh
da	dánština	dansk	Danish
de	němčina	Deutsch	German
dv	divehi	‫ދިވެހި‬	Divehi
dz	dzongkha	རྫོང་ཁ	Dzongkha
ee	eveština	Ɛʋɛgbɛ	Ewe
el	řečtina	Ελληνικά	Greek
en	angličtina	English	English
eo	esperanto	Esperanto	Esperanto
es	španělština	español; castellano	Spanish
et	estonština	Eesti keel	Estonian
eu	baskičtina	euskara	Basque
fa	perština	‫فارسی‬	Persian
ff	fulbština	Fulfulde	Fulah
fi	finština	Suomen kieli	Finnish
fj	fidžijština	vosa Vakaviti	Fijian
fo	faerština	Føroyskt	Faroese
fr	francouzština	français; langue française	French
fy	západofríština	Frysk	Western Frisian
ga	irština	Gaeilge	Irish
gd	skotská gaelština	Gàidhlig	Gaelic
gl	galicijština	Galego	Galician
gn	guaraní	Avañe'ẽ	Guaraní
gu	gudžarátština	ગુજરાતી	Gujarati
gv	manština	Ghaelg	Manx
ha	hauština	‫هَوُسَ‬	Hausa
he	hebrejština	‫עברית‬	Hebrew
hi	hindština	हिन्दी	Hindi
ho	hiri motu	Hiri Motu	Hiri Motu
hr	chorvatština	Hrvatski	Croatian
ht	haitština	Kreyòl ayisyen	Haitian
hu	maďarština	Magyar	Hungarian
hy	arménština	Հայերեն	Armenian
hz	hererština	Otjiherero	Herero
ia	interlingua	Interlingua	Interlingua
id	indonéština	Bahasa Indonesia	Indonesian
ie	interlingue	Interlingue	Interlingue
ig	igbo	Igbo	Igbo
ii	yi	ꆇꉙ	Sichuan Yi
ik	inupiaq	Iñupiaq; Iñupiatun	Inupiaq
io	ido	Ido	Ido
is	islandština	Íslenska	Icelandic
it	italština	Italiano	Italian
iu	inuitština	ᐃᓄᒃᑎᑐᑦ	Inuktitut
ja	japonština	日本語 (にほんご)	Japanese
jv	javánština	basa Jawa	Javanese
ka	gruzínština	ქართული	Georgian
kg	konžština	KiKongo	Kongo
ki	kikujština	Gĩkũyũ	Kikuyu
kj	kuanyama	Kuanyama	Kuanyama
kk	kazaština	Қазақ тілі	Kazakh
kl	grónština	kalaallisut; kalaallit oqaasii	Kalaallisut
km	khmerština	ភាសាខ្មែរ	Khmer
kn	kannadština	ಕನ್ನಡ	Kannada
ko	korejština	한국어 (韓國語); 조선말 (朝鮮語)	Korean
kr	kanurijština	Kanuri	Kanuri
ks	kašmírština	कश्मीरी; ‫كشميري‬	Kashmiri
ku	kurdština	Kurdî; ‫كوردی‬	Kurdish
kv	komijština	коми кыв	Komi
kw	kornština	Kernewek	Cornish
ky	kyrgyzština	кыргыз тили	Kirghiz
la	latina	latine; lingua latina	latin
lb	lucemburština	Lëtzebuergesch	Luxembourgish
lg	gandština	Luganda	Ganda
li	limburština	Limburgs	Limburgish
ln	ngalština	Lingála	Lingala
lo	laoština	ພາສາລາວ	Lao
lt	litevština	lietuvių kalba	Lithuanian
lu	lubština	luba	Luba-Katanga
lv	lotyština	latviešu valoda	Latvian
mg	malgaština	Malagasy fiteny	Malagasy
mh	maršálština	Kajin M̧ajeļ	Marshallese
mi	maorština	te reo Māori	Māori
mk	makedonština	македонски јазик	Macedonian
ml	malajámština	മലയാളം	Malayalam
mn	mongolština	Монгол	Mongolian
mo	moldavština	лимба молдовеняскэ	Moldavian
mr	maráthština	मराठी	Marathi
ms	malajština	bahasa Melayu; ‫بهاس ملايو‬	Malay
mt	maltština	Malti	Maltese
my	barmština	မ္ရန္‌မာစကား (Myanma zaga)	Burmese
na	nauruština	Ekakairũ Naoero	Nauru
nb	bokmål	Norsk (bokmål)	Norwegian Bokmål
nd	severní ndebelština	isiNdebele	North Ndebele
ne	nepálština	नेपाली	Nepali
ng	ndonga	Owambo	Ndonga
nl	nizozemština	Nederlands	Dutch
nn	nynorsk	Nynorsk	Norwegian Nynorsk
no	norština	Norsk	Norwegian
nr	jižní ndebelština	Ndébélé	South Ndebele
nv	navažština	Diné bizaad; Dinékʼehǰí	Navajo
ny	čičevština	chiCheŵa; chinyanja	Chichewa
oc	okcitánština	Occitan	Occitan
oj	odžibvejština	ᐊᓂᔑᓈᐯᒧᐎᓐ	Ojibwa
om	oromština	Afaan Oromoo	Oromo
or	urijština	ଓଡ଼ିଆ	Oriya
os	osetština	Ирон æвзаг	Ossetian
pa	paňdžábština	ਪੰਜਾਬੀ; ‫پنجابی‬	Panjabi
pi	páli	पाऴि	Pāli
pl	polština	Polski	Polish
ps	paštština	‫پښتو‬	Pashto
pt	portugalština	Português	Portuguese
qu	kečuánština	Runa Simi; Kichwa	Quechua
rm	rétorománština	rumantsch grischun	Raeto-Romance
rn	kirundština	kiRundi	Kirundi
ro	rumunština	română	Romanian
ru	ruština	русский язык	Russian
rw	rwandština	Kinyarwanda	Kinyarwanda
sa	sanskrt	संस्कृतम्	Sanskrit
sc	sardština	sardu	Sardinian
sd	sindhština	सिन्धी; ‫سنڌي، سندھی‬	Sindhi
se	severní sámština	Davvisámegiella	Northern Sami
sg	sangština	yângâ tî sängö	Sango
sh	srbochorvatština	Српскохрватски	Serbo-Croatian
si	sinhálština	සිංහල	Sinhala
sk	slovenština	slovenčina	Slovak
sl	slovinština	slovenščina	Slovenian
sm	samojština	gagana fa'a Samoa	Samoan
sn	šonština	chiShona	Shona
so	somálština	Soomaaliga; af Soomaali	Somali
sq	albánština	Shqip	Albanian
sr	srbština	српски језик	Serbian
ss	svazijština	SiSwati	Swati
st	sotština	seSotho	Southern Sotho
su	sundština	Basa Sunda	Sundanese
sv	švédština	Svenska	Swedish
sw	svahilština	Kiswahili	Swahili
ta	tamilština	தமிழ்	Tamil
te	telugština	తెలుగు	Telugu
tg	tádžičtina	тоҷикӣ; toğikī; ‫تاجیکی‬	Tajik
th	thajština	ไทย	Thai
ti	tigriňňa	ትግርኛ	Tigrinya
tk	turkmenština	Türkmen; Түркмен	Turkmen
tl	tagalština	Tagalog	Tagalog
tn	čwanština	seTswana	Tswana
to	tonžština	faka Tonga	Tonga
tr	turečtina	Türkçe	Turkish
ts	tsonga	xiTsonga	Tsonga
tt	tatarština	татарча; tatarça; ‫تاتارچا‬	Tatar
tw	ťwiština	Twi	Twi
ty	tahitština	Reo Mā`ohi	Tahitian
ug	ujgurština	Uyƣurqə; ‫ئۇيغۇرچ ‬	Uighur
uk	ukrajinština	українська мова	Ukrainian
ur	urdština	‫اردو‬	Urdu
uz	uzbečtina	O'zbek; Ўзбек; ‫أۇزبېك‬	Uzbek
ve	luvendština	tshiVenḓa	Venda
vi	vietnamština	Tiếng Việt	Vietnamese
vo	volapük	Volapük	Volapük
wa	valonština	Walon	Walloon
wo	volofština	Wollof	Wolof
xh	xhoština	isiXhosa	Xhosa
yi	jidiš	‫ייִדיש‬	Yiddish
yo	jorubština	Yorùbá	Yoruba
za	čuangština	Saɯ cueŋƅ; Saw cuengh	Zhuang
zh	čínština	中文、汉语、漢語	Chinese
zu	zulština	isiZulu	Zulu
EOT;

}



