<?php

namespace App\Module\Ptv\Util;

class Municipalities
{
    private $map;

    public function __construct()
    {
        $this->map = $this->initialize();
        $this->reverseMap = array_flip($this->map);
    }

    /*
     * NOTE: IDs are zero-padded numbers, not actual integers.
     */
    public function nameToId(string $name) : ?string
    {
        return $this->reverseMap[$name] ?? null;
    }

    public function idToName(string $id) : ?string
    {
        return $this->map[$id] ?? null;
    }

    private function initialize() : array
    {
        $source = '
            005	Alajärvi
            009	Alavieska
            010	Alavus
            016	Asikkala
            018	Askola
            019	Aura
            020	Akaa
            035	Brändö
            043	Eckerö
            046	Enonkoski
            047	Enontekiö
            049	Espoo
            050	Eura
            051	Eurajoki
            052	Evijärvi
            060	Finström
            061	Forssa
            062	Föglö
            065	Geta
            069	Haapajärvi
            071	Haapavesi
            072	Hailuoto
            074	Halsua
            075	Hamina
            076	Hammarland
            077	Hankasalmi
            078	Hanko
            079	Harjavalta
            081	Hartola
            082	Hattula
            086	Hausjärvi
            090	Heinävesi
            091	Helsinki
            092	Vantaa
            097	Hirvensalmi
            098	Hollola
            099	Honkajoki
            102	Huittinen
            103	Humppila
            105	Hyrynsalmi
            106	Hyvinkää
            108	Hämeenkyrö
            109	Hämeenlinna
            111	Heinola
            139	Ii
            140	Iisalmi
            142	Iitti
            143	Ikaalinen
            145	Ilmajoki
            146	Ilomantsi
            148	Inari
            149	Inkoo
            151	Isojoki
            152	Isokyrö
            153	Imatra
            165	Janakkala
            167	Joensuu
            169	Jokioinen
            170	Jomala
            171	Joroinen
            172	Joutsa
            176	Juuka
            177	Juupajoki
            178	Juva
            179	Jyväskylä
            181	Jämijärvi
            182	Jämsä
            186	Järvenpää
            202	Kaarina
            204	Kaavi
            205	Kajaani
            208	Kalajoki
            211	Kangasala
            213	Kangasniemi
            214	Kankaanpää
            216	Kannonkoski
            217	Kannus
            218	Karijoki
            224	Karkkila
            226	Karstula
            230	Karvia
            231	Kaskinen
            232	Kauhajoki
            233	Kauhava
            235	Kauniainen
            236	Kaustinen
            239	Keitele
            240	Kemi
            241	Keminmaa
            244	Kempele
            245	Kerava
            249	Keuruu
            250	Kihniö
            256	Kinnula
            257	Kirkkonummi
            260	Kitee
            261	Kittilä
            263	Kiuruvesi
            265	Kivijärvi
            271	Kokemäki
            272	Kokkola
            273	Kolari
            275	Konnevesi
            276	Kontiolahti
            280	Korsnäs
            284	Koski Tl
            285	Kotka
            286	Kouvola
            287	Kristiinankaupunki
            288	Kruunupyy
            290	Kuhmo
            291	Kuhmoinen
            295	Kumlinge
            297	Kuopio
            300	Kuortane
            301	Kurikka
            304	Kustavi
            305	Kuusamo
            309	Outokumpu
            312	Kyyjärvi
            316	Kärkölä
            317	Kärsämäki
            318	Kökar
            320	Kemijärvi
            322	Kemiönsaari
            398	Lahti
            399	Laihia
            400	Laitila
            402	Lapinlahti
            403	Lappajärvi
            405	Lappeenranta
            407	Lapinjärvi
            408	Lapua
            410	Laukaa
            416	Lemi
            417	Lemland
            418	Lempäälä
            420	Leppävirta
            421	Lestijärvi
            422	Lieksa
            423	Lieto
            425	Liminka
            426	Liperi
            430	Loimaa
            433	Loppi
            434	Loviisa
            435	Luhanka
            436	Lumijoki
            438	Lumparland
            440	Luoto
            441	Luumäki
            444	Lohja
            445	Parainen
            475	Maalahti
            478	Maarianhamina
            480	Marttila
            481	Masku
            483	Merijärvi
            484	Merikarvia
            489	Miehikkälä
            491	Mikkeli
            494	Muhos
            495	Multia
            498	Muonio
            499	Mustasaari
            500	Muurame
            503	Mynämäki
            504	Myrskylä
            505	Mäntsälä
            507	Mäntyharju
            508	Mänttä-Vilppula
            529	Naantali
            531	Nakkila
            535	Nivala
            536	Nokia
            538	Nousiainen
            541	Nurmes
            543	Nurmijärvi
            545	Närpiö
            560	Orimattila
            561	Oripää
            562	Orivesi
            563	Oulainen
            564	Oulu
            576	Padasjoki
            577	Paimio
            578	Paltamo
            580	Parikkala
            581	Parkano
            583	Pelkosenniemi
            584	Perho
            588	Pertunmaa
            592	Petäjävesi
            593	Pieksämäki
            595	Pielavesi
            598	Pietarsaari
            599	Pedersören kunta
            601	Pihtipudas
            604	Pirkkala
            607	Polvijärvi
            608	Pomarkku
            609	Pori
            611	Pornainen
            614	Posio
            615	Pudasjärvi
            616	Pukkila
            619	Punkalaidun
            620	Puolanka
            623	Puumala
            624	Pyhtää
            625	Pyhäjoki
            626	Pyhäjärvi
            630	Pyhäntä
            631	Pyhäranta
            635	Pälkäne
            636	Pöytyä
            638	Porvoo
            678	Raahe
            680	Raisio
            681	Rantasalmi
            683	Ranua
            684	Rauma
            686	Rautalampi
            687	Rautavaara
            689	Rautjärvi
            691	Reisjärvi
            694	Riihimäki
            697	Ristijärvi
            698	Rovaniemi
            700	Ruokolahti
            702	Ruovesi
            704	Rusko
            707	Rääkkylä
            710	Raasepori
            729	Saarijärvi
            732	Salla
            734	Salo
            736	Saltvik
            738	Sauvo
            739	Savitaipale
            740	Savonlinna
            742	Savukoski
            743	Seinäjoki
            746	Sievi
            747	Siikainen
            748	Siikajoki
            749	Siilinjärvi
            751	Simo
            753	Sipoo
            755	Siuntio
            758	Sodankylä
            759	Soini
            761	Somero
            762	Sonkajärvi
            765	Sotkamo
            766	Sottunga
            768	Sulkava
            771	Sund
            777	Suomussalmi
            778	Suonenjoki
            781	Sysmä
            783	Säkylä
            785	Vaala
            790	Sastamala
            791	Siikalatva
            831	Taipalsaari
            832	Taivalkoski
            833	Taivassalo
            834	Tammela
            837	Tampere
            844	Tervo
            845	Tervola
            846	Teuva
            848	Tohmajärvi
            849	Toholampi
            850	Toivakka
            851	Tornio
            853	Turku
            854	Pello
            857	Tuusniemi
            858	Tuusula
            859	Tyrnävä
            886	Ulvila
            887	Urjala
            889	Utajärvi
            890	Utsjoki
            892	Uurainen
            893	Uusikaarlepyy
            895	Uusikaupunki
            905	Vaasa
            908	Valkeakoski
            911	Valtimo
            915	Varkaus
            918	Vehmaa
            921	Vesanto
            922	Vesilahti
            924	Veteli
            925	Vieremä
            927	Vihti
            931	Viitasaari
            934	Vimpeli
            935	Virolahti
            936	Virrat
            941	Vårdö
            946	Vöyri
            976	Ylitornio
            977	Ylivieska
            980	Ylöjärvi
            981	Ypäjä
            989	Ähtäri
            992	Äänekoski
        ';

        preg_match_all("/(\d+)\t(\w+)/mu", $source, $matches);
        $data = array_combine($matches[1], $matches[2]);
        return $data;
    }
}
