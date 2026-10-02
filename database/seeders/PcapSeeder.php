<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\PcapTeam;
use App\Models\PcapPlayer;
use App\Models\PcapMatch;
use App\Models\PcapStanding;

class PcapSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            // Clear existing data cleanly
            PcapStanding::truncate();
            PcapMatch::truncate();
            PcapPlayer::truncate();
            PcapTeam::truncate();

            // 1. TEAMS & ROSTERS (Season 6 Conference 2 Official Roster)
            $teamsData = [
                // ==========================================
                // DIVISION ALPHA (9 Teams)
                // ==========================================
                [
                    'id' => 'pasig-king-pirates',
                    'name' => 'Pasig City King Pirates',
                    'conference' => 'alpha',
                    'roster' => [
                        ['id' => 'pasig-p1', 'name' => 'Daniel Quizon', 'title' => 'GM', 'rating' => null, 'category' => 'O', 'federation' => 'PHI', 'chesscom_username' => 'quizden37'],
                        ['id' => 'pasig-p2', 'name' => 'Michael Concio Jr', 'title' => 'IM', 'rating' => null, 'category' => 'O', 'federation' => 'PHI', 'chesscom_username' => 'jakopogi'],
                        ['id' => 'pasig-p3', 'name' => 'Sherily Cua', 'title' => 'WFM', 'rating' => null, 'category' => 'L', 'federation' => 'PHI', 'chesscom_username' => 'resnullus'],
                        ['id' => 'pasig-p4', 'name' => 'Chito Garma', 'title' => 'IM', 'rating' => null, 'category' => 'S', 'federation' => 'PHI', 'chesscom_username' => 'Garmask'],
                        ['id' => 'pasig-p5', 'name' => 'Idelfonso Datu', 'title' => 'IM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'DanielDatu'],
                        ['id' => 'pasig-p6', 'name' => 'Marc Kevin Labog', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'MarcKevinLabog'],
                        ['id' => 'pasig-p7', 'name' => 'Jerome Villanueva', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'MovesChilling'],
                        ['id' => 'pasig-p8', 'name' => 'Sherwin Tiu', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'Kordapyu'],
                        ['id' => 'pasig-p9', 'name' => 'Omar Bagalacsa', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'T0B3RMOR3Y'],
                        ['id' => 'pasig-p10', 'name' => 'Carl Espallardo', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'coachcarl'],
                        ['id' => 'pasig-p11', 'name' => 'Cromwell Sabado', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'Cromwell_Sabado'],
                        ['id' => 'pasig-p12', 'name' => 'Faye Bernardino', 'title' => null, 'rating' => null, 'category' => 'HG/L', 'federation' => 'PHI', 'chesscom_username' => 'chessterz07'],
                        ['id' => 'pasig-p13', 'name' => 'Gombosuren Munkhgal', 'title' => 'GM', 'rating' => null, 'category' => 'O', 'federation' => 'MGL', 'chesscom_username' => 'Virgo15'],
                    ]
                ],
                [
                    'id' => 'san-juan-predators',
                    'name' => 'San Juan Predators',
                    'conference' => 'alpha',
                    'roster' => [
                        ['id' => 'sj-p1', 'name' => 'Jose Aquino Jr.', 'title' => 'NM', 'rating' => null, 'category' => 'O', 'federation' => 'PHI', 'chesscom_username' => 'USNM_JoseAquinoJr'],
                        ['id' => 'sj-p2', 'name' => 'Karl Viktor Ochoa', 'title' => null, 'rating' => null, 'category' => 'O', 'federation' => 'PHI', 'chesscom_username' => 'BadApp_le'],
                        ['id' => 'sj-p3', 'name' => 'Francois Marie Magpily', 'title' => null, 'rating' => null, 'category' => 'L', 'federation' => 'PHI', 'chesscom_username' => 'francois2002'],
                        ['id' => 'sj-p4', 'name' => 'Joel Anthony Hicap', 'title' => null, 'rating' => null, 'category' => 'S', 'federation' => 'PHI', 'chesscom_username' => 'JoelAMHicap'],
                        ['id' => 'sj-p5', 'name' => 'Rolando Nolte', 'title' => 'IM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'RolandoNolte_PCAP'],
                        ['id' => 'sj-p6', 'name' => 'Narquingden Reyes', 'title' => 'FM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'chidori_boruto'],
                        ['id' => 'sj-p7', 'name' => 'Narquingel Reyes', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'gm_butanding'],
                        ['id' => 'sj-p8', 'name' => 'Julius Gonzales', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'Bad_genuis'],
                        ['id' => 'sj-p9', 'name' => 'Gavin Lloyd Ong', 'title' => 'AGM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'AGM_Gavin_Ong'],
                        ['id' => 'sj-p10', 'name' => 'Jonathan Jota', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'illustradochess'],
                        ['id' => 'sj-p11', 'name' => 'Kevin Arquero', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'Kevin_Arquero'],
                        ['id' => 'sj-p12', 'name' => 'Arvie Lozano', 'title' => null, 'rating' => null, 'category' => 'L', 'federation' => 'PHI', 'chesscom_username' => 'arvielozano1'],
                        ['id' => 'sj-p13', 'name' => 'Domingo Ramos', 'title' => null, 'rating' => null, 'category' => 'S', 'federation' => 'PHI', 'chesscom_username' => 'IMPoloy'],
                        ['id' => 'sj-p14', 'name' => 'Victor Moskalenko', 'title' => 'GM', 'rating' => null, 'category' => 'S', 'federation' => 'ESP', 'chesscom_username' => 'gmMOSKALENKO'],
                    ]
                ],
                [
                    'id' => 'manila-aq-prime',
                    'name' => 'Manila AQ Prime Assets',
                    'conference' => 'alpha',
                    'roster' => [
                        ['id' => 'maq-p1', 'name' => 'Ellan Asuela', 'title' => 'FM', 'rating' => null, 'category' => 'O', 'federation' => 'PHI', 'chesscom_username' => 'ellan_asuela'],
                        ['id' => 'maq-p2', 'name' => 'Dale Bernardo', 'title' => 'NM', 'rating' => null, 'category' => 'O', 'federation' => 'PHI', 'chesscom_username' => 'dale_bernardo'],
                        ['id' => 'maq-p3', 'name' => 'Kylen Joy Mordido', 'title' => 'WIM', 'rating' => null, 'category' => 'L', 'federation' => 'PHI', 'chesscom_username' => 'kylnmd'],
                        ['id' => 'maq-p4', 'name' => 'Ricardo De Guzman', 'title' => 'IM', 'rating' => null, 'category' => 'S', 'federation' => 'PHI', 'chesscom_username' => 'Superkiriks777'],
                        ['id' => 'maq-p5', 'name' => 'Christian Mark Daluz', 'title' => 'FM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'DrMarkenstein29'],
                        ['id' => 'maq-p6', 'name' => 'Ronald Dableo', 'title' => 'IM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'rikimaru101'],
                        ['id' => 'maq-p7', 'name' => 'Jan Emmanuel Garcia', 'title' => 'IM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'superjem123'],
                        ['id' => 'maq-p8', 'name' => 'Ruther Barredo', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'rutherdelinabarredo'],
                        ['id' => 'maq-p9', 'name' => 'Adrian Perez', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'AdrianPerez0610'],
                        ['id' => 'maq-p10', 'name' => 'Jovert Valenzuela', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'Jovert_Valenzuela'],
                        ['id' => 'maq-p11', 'name' => 'Carlos Edgardo Garma', 'title' => 'FM', 'rating' => null, 'category' => 'HG/S', 'federation' => 'PHI', 'chesscom_username' => 'Edgarma_PCAP'],
                        ['id' => 'maq-p12', 'name' => 'Allaney Jia Doroy', 'title' => 'WFM', 'rating' => null, 'category' => 'HG/L', 'federation' => 'PHI', 'chesscom_username' => 'Elektra_01'],
                        ['id' => 'maq-p13', 'name' => 'Paul Sanchez', 'title' => null, 'rating' => null, 'category' => 'O', 'federation' => 'PHI', 'chesscom_username' => 'paulgalas'],
                        ['id' => 'maq-p14', 'name' => 'Mark Paragua', 'title' => 'GM', 'rating' => null, 'category' => 'O', 'federation' => 'PHI', 'chesscom_username' => 'BMWHero2024'],
                        ['id' => 'maq-p15', 'name' => 'Eric Labog Jr.', 'title' => 'IM', 'rating' => null, 'category' => 'O', 'federation' => 'PHI', 'chesscom_username' => 'Eric_Jr_Labog_PCAP'],
                        ['id' => 'maq-p16', 'name' => 'Aaryan Varshney', 'title' => 'GM', 'rating' => null, 'category' => 'O', 'federation' => 'IND', 'chesscom_username' => 'Anaconda1704'],
                    ]
                ],
                [
                    'id' => 'camarines-soaring-eagles',
                    'name' => 'Camarines Soaring Eagles',
                    'conference' => 'alpha',
                    'roster' => [
                        ['id' => 'cam-p1', 'name' => 'Lennon Hart Salgados', 'title' => null, 'rating' => null, 'category' => 'O', 'federation' => 'PHI', 'chesscom_username' => 'LHS29'],
                        ['id' => 'cam-p2', 'name' => 'Giovanni Mejia', 'title' => 'NM', 'rating' => null, 'category' => 'O', 'federation' => 'PHI', 'chesscom_username' => 'Aggressus'],
                        ['id' => 'cam-p3', 'name' => 'Virgenie Ruaya', 'title' => null, 'rating' => null, 'category' => 'L', 'federation' => 'PHI', 'chesscom_username' => 'VirgenieRuaya_PCAP'],
                        ['id' => 'cam-p4', 'name' => 'Edmundo Gatus', 'title' => 'NM', 'rating' => null, 'category' => 'S', 'federation' => 'PHI', 'chesscom_username' => 'Gatsby29'],
                        ['id' => 'cam-p5', 'name' => 'Recarte Tiauson', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'tiauson1122'],
                        ['id' => 'cam-p6', 'name' => 'Jeth Romy Morado', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'StrawHatPirat3'],
                        ['id' => 'cam-p7', 'name' => 'John Marco Balane', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'johnmarco_balane'],
                        ['id' => 'cam-p8', 'name' => 'Coellier Graspela', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'CoelleirGraspela'],
                        ['id' => 'cam-p9', 'name' => 'Ronald Llavanes', 'title' => 'NM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'wanvis'],
                    ]
                ],
                [
                    'id' => 'isabela-knights',
                    'name' => 'Isabela Knights of Alexander',
                    'conference' => 'alpha',
                    'roster' => [
                        ['id' => 'isa-p1', 'name' => 'Yves Ranola', 'title' => 'IM', 'rating' => null, 'category' => 'O', 'federation' => 'PHI', 'chesscom_username' => 'Yunahafk'],
                        ['id' => 'isa-p2', 'name' => 'Manolito Manaois', 'title' => null, 'rating' => null, 'category' => 'O', 'federation' => 'PHI', 'chesscom_username' => 'yman2k9'],
                        ['id' => 'isa-p3', 'name' => 'Nguyen Thi Mai Hung', 'title' => 'WGM', 'rating' => null, 'category' => 'L', 'federation' => 'VIE', 'chesscom_username' => null],
                        ['id' => 'isa-p4', 'name' => 'Gerardo Cabellon', 'title' => 'NM', 'rating' => null, 'category' => 'S', 'federation' => 'PHI', 'chesscom_username' => 'Cararay'],
                        ['id' => 'isa-p5', 'name' => 'Lordwin Espiritu', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'winlord28'],
                        ['id' => 'isa-p6', 'name' => 'Melchor Foronda III', 'title' => 'AGM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'MelchorLforondaIII'],
                        ['id' => 'isa-p7', 'name' => 'Anwar Cabugatan', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'StrongHG_PCAP'],
                        ['id' => 'isa-p8', 'name' => 'Marvin Phua', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'KapeNinja_30'],
                        ['id' => 'isa-p9', 'name' => 'Romy Fagon', 'title' => 'CM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'MaestroFagon06'],
                        ['id' => 'isa-p10', 'name' => 'Joseph Merculio Lalas', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'josephmerculiolalas'],
                        ['id' => 'isa-p11', 'name' => 'Francisco Cabe Jr.', 'title' => null, 'rating' => null, 'category' => 'HG/S', 'federation' => 'PHI', 'chesscom_username' => 'france_10'],
                        ['id' => 'isa-p12', 'name' => 'Diana Banawa', 'title' => null, 'rating' => null, 'category' => 'L', 'federation' => 'PHI', 'chesscom_username' => 'JodielChloe14'],
                        ['id' => 'isa-p13', 'name' => 'Angelo Young', 'title' => 'IM', 'rating' => null, 'category' => 'S', 'federation' => 'PHI', 'chesscom_username' => 'IMAngeloyoung'],
                    ]
                ],
                [
                    'id' => 'quezon-city-simba',
                    'name' => 'IIEE-PSME Quezon City Simba\'s Tribe',
                    'conference' => 'alpha',
                    'roster' => [
                        ['id' => 'qc-simba-p1', 'name' => 'Steven Breckenridge', 'title' => null, 'rating' => null, 'category' => 'O', 'federation' => 'USA', 'chesscom_username' => 'RunStevenRun'],
                        ['id' => 'qc-simba-p2', 'name' => 'Rico Salimbagat', 'title' => 'FM', 'rating' => null, 'category' => 'O', 'federation' => 'PHI', 'chesscom_username' => 'Handsama'],
                        ['id' => 'qc-simba-p3', 'name' => 'Rusela Joya-Magsino', 'title' => 'WNM', 'rating' => null, 'category' => 'L', 'federation' => 'PHI', 'chesscom_username' => 'ruselamags'],
                        ['id' => 'qc-simba-p4', 'name' => 'Leonardo Navarro', 'title' => null, 'rating' => null, 'category' => 'S', 'federation' => 'PHI', 'chesscom_username' => 'lionardo62061'],
                        ['id' => 'qc-simba-p5', 'name' => 'Jony Habla', 'title' => 'FM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'jony79'],
                        ['id' => 'qc-simba-p6', 'name' => 'Joseph Navarro', 'title' => 'CM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'JosephNavarroQCPCAP'],
                        ['id' => 'qc-simba-p7', 'name' => 'Agapay Apollo', 'title' => 'AGM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'Ap0ll025'],
                        ['id' => 'qc-simba-p8', 'name' => 'Francis Talaboc', 'title' => 'AIM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'FrancisTalaboc18'],
                        ['id' => 'qc-simba-p9', 'name' => 'Kristian Paulo Cristobal', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'kpd_crstbl'],
                        ['id' => 'qc-simba-p10', 'name' => 'Norman Madariaga', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'Joman_m'],
                        ['id' => 'qc-simba-p11', 'name' => 'Freddie Talaboc', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'FritZot24'],
                        ['id' => 'qc-simba-p12', 'name' => 'Danilo Ponay', 'title' => null, 'rating' => null, 'category' => 'HG/S', 'federation' => 'PHI', 'chesscom_username' => 'DAN1027'],
                        ['id' => 'qc-simba-p13', 'name' => 'Gerald Ferriol', 'title' => 'NM', 'rating' => null, 'category' => 'O', 'federation' => 'PHI', 'chesscom_username' => 'gmgchesscademy'],
                        ['id' => 'qc-simba-p14', 'name' => 'Nicomedes Alisangco', 'title' => 'NM', 'rating' => null, 'category' => 'O', 'federation' => 'PHI', 'chesscom_username' => 'Nikonovenik'],
                        ['id' => 'qc-simba-p15', 'name' => 'Joseph Galindo', 'title' => 'AGM', 'rating' => null, 'category' => 'O', 'federation' => 'PHI', 'chesscom_username' => 'AGMGALINDO'],
                        ['id' => 'qc-simba-p16', 'name' => 'Jamie Hizon', 'title' => null, 'rating' => null, 'category' => 'L', 'federation' => 'PHI', 'chesscom_username' => 'Hizone'],
                    ]
                ],
                [
                    'id' => 'ocm-cebu-ninos',
                    'name' => 'OCM Cebu Niños',
                    'conference' => 'alpha',
                    'roster' => [
                        ['id' => 'cebu-p1', 'name' => 'Jeriel Manlimbana', 'title' => null, 'rating' => null, 'category' => 'O', 'federation' => 'PHI', 'chesscom_username' => 'JMPawnKiller'],
                        ['id' => 'cebu-p2', 'name' => 'Jezreel Lopez', 'title' => null, 'rating' => null, 'category' => 'O', 'federation' => 'PHI', 'chesscom_username' => 'yurzepol2018'],
                        ['id' => 'cebu-p3', 'name' => 'Marian Calimbo', 'title' => null, 'rating' => null, 'category' => 'L', 'federation' => 'PHI', 'chesscom_username' => 'mrnclmb'],
                        ['id' => 'cebu-p4', 'name' => 'Eladio Lim III', 'title' => null, 'rating' => null, 'category' => 'S', 'federation' => 'PHI', 'chesscom_username' => 'EladioLimIII_PCAP'],
                        ['id' => 'cebu-p5', 'name' => 'Elwin Retanal', 'title' => 'NM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'loverboy33'],
                        ['id' => 'cebu-p6', 'name' => 'Randy Cabuncal', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'jabezattack'],
                        ['id' => 'cebu-p7', 'name' => 'Ariel Potot', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'OCM_Ariel'],
                    ]
                ],
                [
                    'id' => 'mindoro-tamaraws',
                    'name' => 'Mindoro Tamaraws',
                    'conference' => 'alpha',
                    'roster' => [
                        ['id' => 'min-p1', 'name' => 'Liu Xiangyi', 'title' => 'IM', 'rating' => null, 'category' => 'O', 'federation' => 'SGP', 'chesscom_username' => 'chessedeeznuts'],
                        ['id' => 'min-p2', 'name' => 'Joselito Asi', 'title' => 'AGM', 'rating' => null, 'category' => 'O', 'federation' => 'PHI', 'chesscom_username' => 'JoselitoAsi'],
                        ['id' => 'min-p3', 'name' => 'Jacqueline Ilao', 'title' => null, 'rating' => null, 'category' => 'L', 'federation' => 'PHI', 'chesscom_username' => 'PCAP-Jacquilene19'],
                        ['id' => 'min-p4', 'name' => 'Vicente D. Espolon', 'title' => null, 'rating' => null, 'category' => 'S', 'federation' => 'PHI', 'chesscom_username' => 'vicenteespolon'],
                        ['id' => 'min-p5', 'name' => 'Ryan Agbunag', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'dwc_grandmaster'],
                        ['id' => 'min-p6', 'name' => 'Julius Joseph De Ramos', 'title' => 'FM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'shukran0131'],
                        ['id' => 'min-p7', 'name' => 'Nezil Arj Merilles', 'title' => 'AGM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'AGM_Nezil_Merilles'],
                        ['id' => 'min-p8', 'name' => 'Rainier Labay', 'title' => 'NM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'rainski'],
                        ['id' => 'min-p9', 'name' => 'Emmanuel Asi', 'title' => 'AGM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'EMMANUELASI'],
                        ['id' => 'min-p10', 'name' => 'Richard Allen Sicangco', 'title' => 'AIM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'majin_vox'],
                        ['id' => 'min-p11', 'name' => 'Joel Diaz', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'blackrange05'],
                        ['id' => 'min-p12', 'name' => 'Adamah Fuentes', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'Adamah_Fuentes'],
                        ['id' => 'min-p13', 'name' => 'Alvin Dela Cruz', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'SIRALVIN06'],
                        ['id' => 'min-p14', 'name' => 'Jefferson Pascua', 'title' => 'AIM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'jeffersonpascua1217'],
                        ['id' => 'min-p15', 'name' => 'Cesar Cunanan', 'title' => null, 'rating' => null, 'category' => 'HG/S', 'federation' => 'PHI', 'chesscom_username' => 'cunaninsky'],
                        ['id' => 'min-p16', 'name' => 'Cylliz Kaessa Merilles', 'title' => 'AFM', 'rating' => null, 'category' => 'L', 'federation' => 'PHI', 'chesscom_username' => 'Cy07'],
                    ]
                ],
                [
                    'id' => 'laguna-7lakes',
                    'name' => 'Laguna 7 Lakes',
                    'conference' => 'alpha',
                    'roster' => [
                        ['id' => 'lag-p1', 'name' => 'Alvin Roma', 'title' => 'NM', 'rating' => null, 'category' => 'O', 'federation' => 'PHI', 'chesscom_username' => 'RomaAlvin'],
                        ['id' => 'lag-p2', 'name' => 'Norvin Gravillo', 'title' => null, 'rating' => null, 'category' => 'O', 'federation' => 'PHI', 'chesscom_username' => 'nornie'],
                        ['id' => 'lag-p3', 'name' => 'Jollibee Sidaya', 'title' => null, 'rating' => null, 'category' => 'L', 'federation' => 'PHI', 'chesscom_username' => 'Jsidaya'],
                        ['id' => 'lag-p4', 'name' => 'Paul Sumolong', 'title' => null, 'rating' => null, 'category' => 'S', 'federation' => 'PHI', 'chesscom_username' => 'PoinganMustapha'],
                        ['id' => 'lag-p5', 'name' => 'Vince Angelo Medina', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'vincevincevince09'],
                        ['id' => 'lag-p6', 'name' => 'Joemarie Villadelgado', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'bujong2010'],
                        ['id' => 'lag-p7', 'name' => 'Reynaldo Pacia Jr.', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'Reypacia'],
                    ]
                ],
                // ==========================================
                // DIVISION OMEGA (9 Teams)
                // ==========================================
                [
                    'id' => 'toledo-xignex-trojans',
                    'name' => 'Toledo-Xignex Trojans',
                    'conference' => 'omega',
                    'roster' => [
                        ['id' => 'tol-p1', 'name' => 'Rogelio Barcenilla Jr.', 'title' => 'GM', 'rating' => null, 'category' => 'O', 'federation' => 'PHI', 'chesscom_username' => 'macho_guwapito'],
                        ['id' => 'tol-p2', 'name' => 'Joel Banawa', 'title' => 'IM', 'rating' => null, 'category' => 'O', 'federation' => 'PHI', 'chesscom_username' => 'pcappractice'],
                        ['id' => 'tol-p3', 'name' => 'Cherry Ann Mejia', 'title' => 'WFM', 'rating' => null, 'category' => 'L', 'federation' => 'PHI', 'chesscom_username' => 'Me_ch_an'],
                        ['id' => 'tol-p4', 'name' => 'Cesar Mariano', 'title' => 'NM', 'rating' => null, 'category' => 'S', 'federation' => 'PHI', 'chesscom_username' => 'Cesar_Mariano_NM'],
                        ['id' => 'tol-p5', 'name' => 'Kim Steven Yap', 'title' => 'IM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'kimstevenyap'],
                        ['id' => 'tol-p6', 'name' => 'Joel Pimentel', 'title' => 'IM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'JayDee0305'],
                        ['id' => 'tol-p7', 'name' => 'Diego Abraham Capariño', 'title' => 'NM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'Aster_D'],
                        ['id' => 'tol-p8', 'name' => 'Allan Pason', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'simpledanger'],
                        ['id' => 'tol-p9', 'name' => 'John Dave Lavandero', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'JohnDave1234'],
                        ['id' => 'tol-p10', 'name' => 'Virgen Gil Ruaya', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'rurully'],
                        ['id' => 'tol-p11', 'name' => 'Barlo Nadera', 'title' => 'IM', 'rating' => null, 'category' => 'HG/S', 'federation' => 'PHI', 'chesscom_username' => 'NaderaIM'],
                        ['id' => 'tol-p12', 'name' => 'Rico Mascariñas', 'title' => 'IM', 'rating' => null, 'category' => 'HG/S', 'federation' => 'PHI', 'chesscom_username' => 'IMMascarinas'],
                        ['id' => 'tol-p13', 'name' => 'Cyril Ortega', 'title' => 'NM', 'rating' => null, 'category' => 'HG/S', 'federation' => 'PHI', 'chesscom_username' => 'cyril62'],
                        ['id' => 'tol-p14', 'name' => 'Bernadette Galas', 'title' => 'WIM', 'rating' => null, 'category' => 'L', 'federation' => 'PHI', 'chesscom_username' => 'Berga1201'],
                        ['id' => 'tol-p15', 'name' => 'Melizah Ruth Carreon', 'title' => 'AGM', 'rating' => null, 'category' => 'L', 'federation' => 'PHI', 'chesscom_username' => 'Melizah_PCAP'],
                        ['id' => 'tol-p16', 'name' => 'Enrico Sevillano', 'title' => 'GM', 'rating' => null, 'category' => 'S', 'federation' => 'USA', 'chesscom_username' => 'Taxidermist'],
                    ]
                ],
                [
                    'id' => 'sudeco-manila-indios-bravos',
                    'name' => 'Sudeco-Manila Indios Bravos',
                    'conference' => 'omega',
                    'roster' => [
                        ['id' => 'mlm-p1', 'name' => 'Yoseph Taher', 'title' => 'IM', 'rating' => null, 'category' => 'O', 'federation' => 'INA', 'chesscom_username' => 'yosephtaher'],
                        ['id' => 'mlm-p2', 'name' => 'Ernie Maraan', 'title' => null, 'rating' => null, 'category' => 'O', 'federation' => 'PHI', 'chesscom_username' => 'johnernie'],
                        ['id' => 'mlm-p3', 'name' => 'Shania Mae Mendoza', 'title' => 'WFM', 'rating' => null, 'category' => 'L', 'federation' => 'PHI', 'chesscom_username' => 'ShaniaMendoza_PCAP'],
                        ['id' => 'mlm-p4', 'name' => 'Rogelio Antonio', 'title' => 'GM', 'rating' => null, 'category' => 'S', 'federation' => 'PHI', 'chesscom_username' => 'gmjoey1'],
                        ['id' => 'mlm-p5', 'name' => 'Paulo Bersamina', 'title' => 'IM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'fastestmindalive'],
                        ['id' => 'mlm-p6', 'name' => 'David Elorta', 'title' => 'FM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'elorta_david'],
                        ['id' => 'mlm-p7', 'name' => 'Nelson Mariano III', 'title' => 'FM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'DrDreBeats'],
                        ['id' => 'mlm-p8', 'name' => 'Daryl Samantilla', 'title' => 'NM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'TheGhostSamurai'],
                        ['id' => 'mlm-p9', 'name' => 'Jhulo Goloran', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'JhuloGoloran'],
                        ['id' => 'mlm-p10', 'name' => 'Genghis Imperial', 'title' => 'CM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'Genghis_Imperial'],
                        ['id' => 'mlm-p11', 'name' => 'Expedito De Leon', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'Expi_2026'],
                        ['id' => 'mlm-p12', 'name' => 'Paulexander Elauria', 'title' => null, 'rating' => null, 'category' => 'S', 'federation' => 'PHI', 'chesscom_username' => 'rednaxxx'],
                        ['id' => 'mlm-p13', 'name' => 'Mario Mangubat', 'title' => 'NM', 'rating' => null, 'category' => 'S', 'federation' => 'PHI', 'chesscom_username' => 'mariomangubat'],
                        ['id' => 'mlm-p14', 'name' => 'Roldan De Leon', 'title' => null, 'rating' => null, 'category' => 'S', 'federation' => 'PHI', 'chesscom_username' => 'Cachopov'],
                    ]
                ],
                [
                    'id' => 'bacolod-blitzers',
                    'name' => 'Bacolod Blitzers',
                    'conference' => 'omega',
                    'roster' => [
                        ['id' => 'bac-p1', 'name' => 'Felix II Gonzales', 'title' => null, 'rating' => null, 'category' => 'O', 'federation' => 'PHI', 'chesscom_username' => 'PINTARJAYA_2013'],
                        ['id' => 'bac-p2', 'name' => 'Thesius Benitez', 'title' => null, 'rating' => null, 'category' => 'O', 'federation' => 'PHI', 'chesscom_username' => 'links44'],
                        ['id' => 'bac-p3', 'name' => 'Eden Agape Tumbos Ting', 'title' => null, 'rating' => null, 'category' => 'L', 'federation' => 'PHI', 'chesscom_username' => 'PCAP_eat'],
                        ['id' => 'bac-p4', 'name' => 'Josevito Tapulgo', 'title' => null, 'rating' => null, 'category' => 'S', 'federation' => 'PHI', 'chesscom_username' => 'Josevito_Tapulgo81'],
                        ['id' => 'bac-p5', 'name' => 'Edsel Montoya', 'title' => 'NM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'edsel_montoya06'],
                        ['id' => 'bac-p6', 'name' => 'Ted Ian Montoyo', 'title' => 'NM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'tedian_montoyo'],
                        ['id' => 'bac-p7', 'name' => 'Ian Cris Henry Udani', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'D-Infiltrator'],
                        ['id' => 'bac-p8', 'name' => 'Rolando Andador', 'title' => 'NM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'Philjr95'],
                        ['id' => 'bac-p9', 'name' => 'Romeo Sadia', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'Romeo_Sadia_III'],
                        ['id' => 'bac-p10', 'name' => 'Eric Abanco', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'ericchess2018'],
                        ['id' => 'bac-p11', 'name' => 'Danny Maersk Mangao', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'danmaersk'],
                        ['id' => 'bac-p12', 'name' => 'Edwin Tan', 'title' => 'NM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'dwenski'],
                        ['id' => 'bac-p13', 'name' => 'Dr. Melben Jochico', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'melbenjochico'],
                        ['id' => 'bac-p14', 'name' => 'Alfred III Acaling', 'title' => null, 'rating' => null, 'category' => 'HG/S', 'federation' => 'PHI', 'chesscom_username' => null],
                        ['id' => 'bac-p15', 'name' => 'Eduardo Sase', 'title' => null, 'rating' => null, 'category' => 'HG/S', 'federation' => 'PHI', 'chesscom_username' => 'eduardosase1958'],
                    ]
                ],
                [
                    'id' => 'cagayan-kings',
                    'name' => 'Cagayan Kings',
                    'conference' => 'omega',
                    'roster' => [
                        ['id' => 'cag-p1', 'name' => 'Don Tyrone Delos Santos', 'title' => null, 'rating' => null, 'category' => 'O', 'federation' => 'PHI', 'chesscom_username' => 'DonTyroneDelosSantos_PCAP'],
                        ['id' => 'cag-p2', 'name' => 'Ali Guya', 'title' => null, 'rating' => null, 'category' => 'O', 'federation' => 'PHI', 'chesscom_username' => 'alicguya'],
                        ['id' => 'cag-p3', 'name' => 'April Joy Ramos', 'title' => null, 'rating' => null, 'category' => 'L', 'federation' => 'PHI', 'chesscom_username' => 'JayrilRamos'],
                        ['id' => 'cag-p4', 'name' => 'Gary-Legaspi', 'title' => null, 'rating' => null, 'category' => 'S', 'federation' => 'PHI', 'chesscom_username' => 'garyjing'],
                        ['id' => 'cag-p5', 'name' => 'Jake Tumaliuan', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'JAKE_TUMALIUAN'],
                        ['id' => 'cag-p6', 'name' => 'Marc Francis Balanay', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'BalanayMarcFrancis'],
                        ['id' => 'cag-p7', 'name' => 'Bencelie Fernandez', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'BencelieFernandez'],
                        ['id' => 'cag-p8', 'name' => 'Alexander Jude S. Malabad', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'MasterChess_23'],
                        ['id' => 'cag-p9', 'name' => 'Marfred Sanchez', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'Mafy_TG_777'],
                        ['id' => 'cag-p10', 'name' => 'Joey Cabaya', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'JoeySaludCabaya'],
                        ['id' => 'cag-p11', 'name' => 'Robert James Pe Benito', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => '1RJFISCHER'],
                        ['id' => 'cag-p12', 'name' => 'John Robert Bumatay', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'JR0bertBumatay_PCAP'],
                        ['id' => 'cag-p13', 'name' => 'Laurence Wilfred Dumadag', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'larrycodes'],
                        ['id' => 'cag-p14', 'name' => 'Jose Jude Antonio Ulanday', 'title' => null, 'rating' => null, 'category' => 'S', 'federation' => 'PHI', 'chesscom_username' => 'Kyle_Ulanday25'],
                        ['id' => 'cag-p15', 'name' => 'Harison Maamo', 'title' => null, 'rating' => null, 'category' => 'O', 'federation' => 'PHI', 'chesscom_username' => 'harison_maamo44'],
                        ['id' => 'cag-p16', 'name' => 'Alexei Barsov', 'title' => null, 'rating' => null, 'category' => 'S', 'federation' => 'UZB', 'chesscom_username' => 'Barsov1966'],
                    ]
                ],
                [
                    'id' => 'cavite-spartans',
                    'name' => 'Cavite Spartans',
                    'conference' => 'omega',
                    'roster' => [
                        ['id' => 'cav-p1', 'name' => 'Jim Dean', 'title' => 'FM', 'rating' => null, 'category' => 'O', 'federation' => 'USA', 'chesscom_username' => 'CoachDean78'],
                        ['id' => 'cav-p2', 'name' => 'Cathrino Pestaño', 'title' => 'NM', 'rating' => null, 'category' => 'O', 'federation' => 'PHI', 'chesscom_username' => 'cickings'],
                        ['id' => 'cav-p3', 'name' => 'Jessica Aguilar', 'title' => null, 'rating' => null, 'category' => 'L', 'federation' => 'PHI', 'chesscom_username' => 'aki_1792'],
                        ['id' => 'cav-p4', 'name' => 'Dioniver Medrano', 'title' => null, 'rating' => null, 'category' => 'S', 'federation' => 'PHI', 'chesscom_username' => 'bobotmedrano'],
                        ['id' => 'cav-p5', 'name' => 'Voltaire Marc Paraguya', 'title' => 'AGM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'marc_paraguya'],
                        ['id' => 'cav-p6', 'name' => 'Jayson Visca', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'VT_Visca'],
                        ['id' => 'cav-p7', 'name' => 'Marco Jay Mabasa', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'chessmakoy'],
                        ['id' => 'cav-p8', 'name' => 'Renie Malupa', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'Renie_L_Malupa'],
                        ['id' => 'cav-p9', 'name' => 'Jeffrey Romera', 'title' => 'AIM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'JeffreyRomera'],
                        ['id' => 'cav-p10', 'name' => 'Aldrin Pasno', 'title' => 'AIM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'PasnoAL'],
                        ['id' => 'cav-p11', 'name' => 'Albert Pasno', 'title' => 'AFM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'TantAntaN08'],
                        ['id' => 'cav-p12', 'name' => 'Jayson Danday', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'Baeseek'],
                        ['id' => 'cav-p13', 'name' => 'Rodolf Perez', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'Rudolf_Perez05'],
                        ['id' => 'cav-p14', 'name' => 'Robvy Jemuel Wong', 'title' => 'AIM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'Xavier_Vincent'],
                        ['id' => 'cav-p15', 'name' => 'Christine Muli', 'title' => 'AIM', 'rating' => null, 'category' => 'HG/L', 'federation' => 'PHI', 'chesscom_username' => 'jinhernandez31'],
                        ['id' => 'cav-p16', 'name' => 'Eduardo Tunguia', 'title' => 'AFM', 'rating' => null, 'category' => 'HG/S', 'federation' => 'PHI', 'chesscom_username' => 'SirEduardo12'],
                    ]
                ],
                [
                    'id' => 'arriba-iriga-oragons',
                    'name' => 'Arriba Iriga Oragons',
                    'conference' => 'omega',
                    'roster' => [
                        ['id' => 'iri-p1', 'name' => 'Alji Cantonjos', 'title' => null, 'rating' => null, 'category' => 'O', 'federation' => 'PHI', 'chesscom_username' => 'aljicantonjos14'],
                        ['id' => 'iri-p2', 'name' => 'Vladimir Gonzales', 'title' => null, 'rating' => null, 'category' => 'O', 'federation' => 'PHI', 'chesscom_username' => 'VPGonzales98'],
                        ['id' => 'iri-p3', 'name' => 'Isabel Palibino', 'title' => null, 'rating' => null, 'category' => 'L', 'federation' => 'PHI', 'chesscom_username' => 'palibinoisabel_PCAP'],
                        ['id' => 'iri-p4', 'name' => 'Roger Pesimo', 'title' => null, 'rating' => null, 'category' => 'S', 'federation' => 'PHI', 'chesscom_username' => 'rogerpesimo'],
                        ['id' => 'iri-p5', 'name' => 'Joeven Polsotin', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'joevenpolsotin'],
                        ['id' => 'iri-p6', 'name' => 'Fr. Emil Valeza', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'FrEmil_V'],
                        ['id' => 'iri-p7', 'name' => 'Glennen Artuz', 'title' => 'NM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'zutragg'],
                        ['id' => 'iri-p8', 'name' => 'Jeffrey Vegas', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'jeffreyvegas'],
                        ['id' => 'iri-p9', 'name' => 'Jayvee Relleve', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'Jayveerelleve'],
                        ['id' => 'iri-p10', 'name' => 'Paul Christian Barroga', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'PaulChristianBarroga'],
                        ['id' => 'iri-p11', 'name' => 'Johnlyn Buenaventura', 'title' => null, 'rating' => null, 'category' => 'HG/L', 'federation' => 'PHI', 'chesscom_username' => 'Piratesj'],
                        ['id' => 'iri-p12', 'name' => 'Samantha Glo Revita-Formoso', 'title' => null, 'rating' => null, 'category' => 'HG/L', 'federation' => 'PHI', 'chesscom_username' => 'samrevita'],
                        ['id' => 'iri-p13', 'name' => 'Veron Camposano', 'title' => null, 'rating' => null, 'category' => 'HG/L', 'federation' => 'PHI', 'chesscom_username' => 'mvkcamposano'],
                        ['id' => 'iri-p14', 'name' => 'Eldin Eroma', 'title' => null, 'rating' => null, 'category' => 'O', 'federation' => 'PHI', 'chesscom_username' => 'EldinEroma'],
                    ]
                ],
                [
                    'id' => 'rizal-batch-towers',
                    'name' => 'Rizal Batch Towers',
                    'conference' => 'omega',
                    'roster' => [
                        ['id' => 'riz-p1', 'name' => 'Raymond Salcedo', 'title' => 'NM', 'rating' => null, 'category' => 'O', 'federation' => 'PHI', 'chesscom_username' => 'monsky123'],
                        ['id' => 'riz-p2', 'name' => 'Carlo Magno Rosaupan', 'title' => 'NM', 'rating' => null, 'category' => 'O', 'federation' => 'PHI', 'chesscom_username' => 'NMCarloRosaupan'],
                        ['id' => 'riz-p3', 'name' => 'Irene Rivera', 'title' => null, 'rating' => null, 'category' => 'L', 'federation' => 'PHI', 'chesscom_username' => 'rainsky_02'],
                        ['id' => 'riz-p4', 'name' => 'Henry Villanueva', 'title' => null, 'rating' => null, 'category' => 'S', 'federation' => 'PHI', 'chesscom_username' => 'henrie_villanueva'],
                        ['id' => 'riz-p5', 'name' => 'Givy M. Bartolome', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'gv_bartolomew'],
                        ['id' => 'riz-p6', 'name' => 'Walt Alen Talan', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'waltallen11'],
                        ['id' => 'riz-p7', 'name' => 'Marlon Constantino', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'MarlonConstantino'],
                        ['id' => 'riz-p8', 'name' => 'Leo Anthony Rabulan', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'Leo_Rabulan'],
                        ['id' => 'riz-p9', 'name' => 'Sonny B Dela Rosa', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'naruffytsu95'],
                        ['id' => 'riz-p10', 'name' => 'Eric Oandra', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'erukoy'],
                        ['id' => 'riz-p11', 'name' => 'Renato Cruz Jr.', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'ako_C_renzo'],
                        ['id' => 'riz-p12', 'name' => 'John Perzeus S. Orozco', 'title' => null, 'rating' => null, 'category' => 'O', 'federation' => 'PHI', 'chesscom_username' => 'ZeusOrozcoPCAP'],
                        ['id' => 'riz-p13', 'name' => 'Jamaica Marie Lagrio', 'title' => null, 'rating' => null, 'category' => 'L', 'federation' => 'PHI', 'chesscom_username' => 'Jamsteeeer'],
                    ]
                ],
                [
                    'id' => 'zamboanga-sultans',
                    'name' => 'Zamboanga Sultans',
                    'conference' => 'omega',
                    'roster' => [
                        ['id' => 'zam-p1', 'name' => 'Jones Maghuyop', 'title' => null, 'rating' => null, 'category' => 'O', 'federation' => 'PHI', 'chesscom_username' => 'jozam123'],
                        ['id' => 'zam-p2', 'name' => 'Jordan Gadayan', 'title' => null, 'rating' => null, 'category' => 'O', 'federation' => 'PHI', 'chesscom_username' => 'JordanGadayan_PCAP'],
                        ['id' => 'zam-p3', 'name' => 'Sarah Mae Chua', 'title' => null, 'rating' => null, 'category' => 'L', 'federation' => 'PHI', 'chesscom_username' => 'SarahChua_PCAP'],
                        ['id' => 'zam-p4', 'name' => 'Francisco Delos Santos', 'title' => null, 'rating' => null, 'category' => 'S', 'federation' => 'PHI', 'chesscom_username' => 'Franciscodelossantos61'],
                        ['id' => 'zam-p5', 'name' => 'Robick Vohn Villa', 'title' => 'NM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'AGM_RobickVohn'],
                        ['id' => 'zam-p6', 'name' => 'Abdulhan Agga', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'Gag47'],
                        ['id' => 'zam-p7', 'name' => 'Faizal Najar', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'BobbyFaizal12'],
                        ['id' => 'zam-p8', 'name' => 'Rey Reyes', 'title' => 'AGM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'ReyReyes1'],
                        ['id' => 'zam-p9', 'name' => 'Zulfikar Sali', 'title' => 'NM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'Sultan_Z'],
                        ['id' => 'zam-p10', 'name' => 'Rodrigo Villa Jr.', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'rod_villa_zambo'],
                        ['id' => 'zam-p11', 'name' => 'Engr. Nathan Cornella', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'NathanCee'],
                        ['id' => 'zam-p12', 'name' => 'Atty Anthony Orbe', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'Orbe_Cliburn'],
                        ['id' => 'zam-p13', 'name' => 'Sarri Subahani', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'SarriSubahani'],
                        ['id' => 'zam-p14', 'name' => 'Dr Saibzur Edding', 'title' => null, 'rating' => null, 'category' => 'HG/S', 'federation' => 'PHI', 'chesscom_username' => 'EdibuO'],
                    ]
                ],
                [
                    'id' => 'qc-chessmates-stallions',
                    'name' => 'QC - Chessmates Stallions',
                    'conference' => 'omega',
                    'roster' => [
                        ['id' => 'qc-stal-p1', 'name' => 'Andrew Elpedes', 'title' => null, 'rating' => null, 'category' => 'O', 'federation' => 'PHI', 'chesscom_username' => 'andrewelpedes'],
                        ['id' => 'qc-stal-p2', 'name' => 'Robert Cacho', 'title' => null, 'rating' => null, 'category' => 'O', 'federation' => 'PHI', 'chesscom_username' => 'ultimate_warrior01-pcap'],
                        ['id' => 'qc-stal-p3', 'name' => 'Robelle De Jesus', 'title' => null, 'rating' => null, 'category' => 'L', 'federation' => 'PHI', 'chesscom_username' => 'robs_e4'],
                        ['id' => 'qc-stal-p4', 'name' => 'Bernardo Yap', 'title' => null, 'rating' => null, 'category' => 'S', 'federation' => 'PHI', 'chesscom_username' => 'Ukccbern'],
                        ['id' => 'qc-stal-p5', 'name' => 'Arthur Macaspac', 'title' => 'NM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'soadvi'],
                        ['id' => 'qc-stal-p6', 'name' => 'Adriel Nicolas Macaspac', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'bigmacwchess'],
                        ['id' => 'qc-stal-p7', 'name' => 'Matthew Gotel', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'DeathX_Hyperion'],
                        ['id' => 'qc-stal-p8', 'name' => 'Marcus Fratkin', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'mr_nobody6726'],
                        ['id' => 'qc-stal-p9', 'name' => 'Romelito Lucion', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI', 'chesscom_username' => 'Romelito001'],
                        ['id' => 'qc-stal-p10', 'name' => 'Rodel Juadines', 'title' => null, 'rating' => null, 'category' => 'S', 'federation' => 'PHI', 'chesscom_username' => null],
                        ['id' => 'qc-stal-p11', 'name' => 'Stephen Manzanero', 'title' => null, 'rating' => null, 'category' => 'O', 'federation' => 'PHI', 'chesscom_username' => 'Gmwesley25'],
                        ['id' => 'qc-stal-p12', 'name' => 'John Lee Antonio', 'title' => null, 'rating' => null, 'category' => 'O', 'federation' => 'PHI', 'chesscom_username' => 'MasterLee20'],
                        ['id' => 'qc-stal-p13', 'name' => 'Mervin Lumidao', 'title' => null, 'rating' => null, 'category' => 'O', 'federation' => 'PHI', 'chesscom_username' => 'MervinxRojin'],
                    ]
                ],
            ];

            foreach ($teamsData as $teamItem) {
                $roster = $teamItem['roster'];
                unset($teamItem['roster']);

                $teamItem['wins'] = 0;
                $teamItem['losses'] = 0;
                $teamItem['draws'] = 0;
                $teamItem['match_points'] = 0;
                $teamItem['board_points_for'] = 0.0;
                $teamItem['board_points_against'] = 0.0;

                $team = PcapTeam::create($teamItem);

                foreach ($roster as $playerItem) {
                    $playerItem['team_id'] = $team->id;
                    PcapPlayer::create($playerItem);
                }
            }

            // 2. MATCHDAY FIXTURES (Round 1 Official Results - Wesley So Cup)
            $createEmptyBoards = function () {
                $roles = [
                    ['role' => 'top_gun', 'label' => 'Board 1 • Top Gun'],
                    ['role' => 'top_gun', 'label' => 'Board 2 • Top Gun'],
                    ['role' => 'lady', 'label' => 'Board 3 • Lady Board'],
                    ['role' => 'senior', 'label' => 'Board 4 • Senior (60+)'],
                    ['role' => 'homegrown', 'label' => 'Board 5 • Homegrown'],
                    ['role' => 'homegrown', 'label' => 'Board 6 • Homegrown'],
                    ['role' => 'homegrown', 'label' => 'Board 7 • Homegrown'],
                ];
                return array_map(function ($r, $i) {
                    return [
                        'boardNumber' => $i + 1,
                        'role' => $r['role'],
                        'roleLabel' => $r['label'],
                        'playerA' => ['name' => '', 'title' => null, 'rating' => 2000],
                        'playerB' => ['name' => '', 'title' => null, 'rating' => 2000],
                        'blitzScoreA' => 0.0,
                        'blitzScoreB' => 0.0,
                        'rapidScoreA' => 0.0,
                        'rapidScoreB' => 0.0,
                    ];
                }, $roles, array_keys($roles));
            };

            $matchesData = [
                // --- DIVISION ALPHA ---
                [
                    'id' => 'match-rd1-alpha-1',
                    'round' => 1,
                    'conference' => 'alpha',
                    'season' => '2026 Season • Wesley So Cup',
                    'date' => 'Round 1',
                    'time' => '7:00 PM PHT',
                    'status' => 'completed',
                    'team_a_id' => 'san-juan-predators',
                    'team_b_id' => 'laguna-7lakes',
                    'score_a' => 18.0,
                    'score_b' => 3.0,
                    'blitz_score_a' => 0.0,
                    'blitz_score_b' => 0.0,
                    'rapid_score_a' => 0.0,
                    'rapid_score_b' => 0.0,
                    'boards' => $createEmptyBoards(),
                ],
                [
                    'id' => 'match-rd1-alpha-2',
                    'round' => 1,
                    'conference' => 'alpha',
                    'season' => '2026 Season • Wesley So Cup',
                    'date' => 'Round 1',
                    'time' => '7:00 PM PHT',
                    'status' => 'completed',
                    'team_a_id' => 'manila-aq-prime',
                    'team_b_id' => 'mindoro-tamaraws',
                    'score_a' => 21.0,
                    'score_b' => 0.0,
                    'blitz_score_a' => 0.0,
                    'blitz_score_b' => 0.0,
                    'rapid_score_a' => 0.0,
                    'rapid_score_b' => 0.0,
                    'boards' => $createEmptyBoards(),
                ],
                [
                    'id' => 'match-rd1-alpha-3',
                    'round' => 1,
                    'conference' => 'alpha',
                    'season' => '2026 Season • Wesley So Cup',
                    'date' => 'Round 1',
                    'time' => '7:00 PM PHT',
                    'status' => 'completed',
                    'team_a_id' => 'camarines-soaring-eagles',
                    'team_b_id' => 'ocm-cebu-ninos',
                    'score_a' => 15.0,
                    'score_b' => 6.0,
                    'blitz_score_a' => 0.0,
                    'blitz_score_b' => 0.0,
                    'rapid_score_a' => 0.0,
                    'rapid_score_b' => 0.0,
                    'boards' => $createEmptyBoards(),
                ],
                [
                    'id' => 'match-rd1-alpha-4',
                    'round' => 1,
                    'conference' => 'alpha',
                    'season' => '2026 Season • Wesley So Cup',
                    'date' => 'Round 1',
                    'time' => '7:00 PM PHT',
                    'status' => 'completed',
                    'team_a_id' => 'isabela-knights',
                    'team_b_id' => 'quezon-city-simba',
                    'score_a' => 7.5,
                    'score_b' => 13.5,
                    'blitz_score_a' => 0.0,
                    'blitz_score_b' => 0.0,
                    'rapid_score_a' => 0.0,
                    'rapid_score_b' => 0.0,
                    'boards' => $createEmptyBoards(),
                ],

                // --- DIVISION OMEGA ---
                [
                    'id' => 'match-rd1-omega-1',
                    'round' => 1,
                    'conference' => 'omega',
                    'season' => '2026 Season • Wesley So Cup',
                    'date' => 'Round 1',
                    'time' => '7:00 PM PHT',
                    'status' => 'completed',
                    'team_a_id' => 'sudeco-manila-indios-bravos',
                    'team_b_id' => 'qc-chessmates-stallions',
                    'score_a' => 11.5,
                    'score_b' => 9.5,
                    'blitz_score_a' => 0.0,
                    'blitz_score_b' => 0.0,
                    'rapid_score_a' => 0.0,
                    'rapid_score_b' => 0.0,
                    'boards' => $createEmptyBoards(),
                ],
                [
                    'id' => 'match-rd1-omega-2',
                    'round' => 1,
                    'conference' => 'omega',
                    'season' => '2026 Season • Wesley So Cup',
                    'date' => 'Round 1',
                    'time' => '7:00 PM PHT',
                    'status' => 'completed',
                    'team_a_id' => 'bacolod-blitzers',
                    'team_b_id' => 'zamboanga-sultans',
                    'score_a' => 13.0,
                    'score_b' => 8.0,
                    'blitz_score_a' => 0.0,
                    'blitz_score_b' => 0.0,
                    'rapid_score_a' => 0.0,
                    'rapid_score_b' => 0.0,
                    'boards' => $createEmptyBoards(),
                ],
                [
                    'id' => 'match-rd1-omega-3',
                    'round' => 1,
                    'conference' => 'omega',
                    'season' => '2026 Season • Wesley So Cup',
                    'date' => 'Round 1',
                    'time' => '7:00 PM PHT',
                    'status' => 'completed',
                    'team_a_id' => 'cagayan-kings',
                    'team_b_id' => 'rizal-batch-towers',
                    'score_a' => 15.5,
                    'score_b' => 5.5,
                    'blitz_score_a' => 0.0,
                    'blitz_score_b' => 0.0,
                    'rapid_score_a' => 0.0,
                    'rapid_score_b' => 0.0,
                    'boards' => $createEmptyBoards(),
                ],
                [
                    'id' => 'match-rd1-omega-4',
                    'round' => 1,
                    'conference' => 'omega',
                    'season' => '2026 Season • Wesley So Cup',
                    'date' => 'Round 1',
                    'time' => '7:00 PM PHT',
                    'status' => 'completed',
                    'team_a_id' => 'cavite-spartans',
                    'team_b_id' => 'arriba-iriga-oragons',
                    'score_a' => 13.0,
                    'score_b' => 8.0,
                    'blitz_score_a' => 0.0,
                    'blitz_score_b' => 0.0,
                    'rapid_score_a' => 0.0,
                    'rapid_score_b' => 0.0,
                    'boards' => $createEmptyBoards(),
                ],
            ];

            foreach ($matchesData as $matchItem) {
                PcapMatch::create($matchItem);
            }

            // 3. STANDINGS & TEAM STATS CALCULATION
            $standingsMap = [];
            foreach ($teamsData as $teamItem) {
                $standingsMap[$teamItem['id']] = [
                    'team_id' => $teamItem['id'],
                    'conference' => $teamItem['conference'],
                    'matches_played' => 0,
                    'won' => 0,
                    'drawn' => 0,
                    'lost' => 0,
                    'match_points' => 0,
                    'board_points_for' => 0.0,
                    'board_points_against' => 0.0,
                    'board_points_diff' => 0.0,
                    'form' => [],
                ];
            }

            foreach ($matchesData as $m) {
                if ($m['status'] !== 'completed') continue;

                $tA = $m['team_a_id'];
                $tB = $m['team_b_id'];
                $sA = (float) $m['score_a'];
                $sB = (float) $m['score_b'];

                $standingsMap[$tA]['matches_played']++;
                $standingsMap[$tB]['matches_played']++;
                $standingsMap[$tA]['board_points_for'] += $sA;
                $standingsMap[$tA]['board_points_against'] += $sB;
                $standingsMap[$tB]['board_points_for'] += $sB;
                $standingsMap[$tB]['board_points_against'] += $sA;

                if ($sA > $sB) {
                    $standingsMap[$tA]['won']++;
                    $standingsMap[$tA]['match_points'] += 1;
                    $standingsMap[$tA]['form'][] = 'W';

                    $standingsMap[$tB]['lost']++;
                    $standingsMap[$tB]['form'][] = 'L';
                } elseif ($sA < $sB) {
                    $standingsMap[$tB]['won']++;
                    $standingsMap[$tB]['match_points'] += 1;
                    $standingsMap[$tB]['form'][] = 'W';

                    $standingsMap[$tA]['lost']++;
                    $standingsMap[$tA]['form'][] = 'L';
                } else {
                    $standingsMap[$tA]['drawn']++;
                    $standingsMap[$tA]['form'][] = 'D';
                    $standingsMap[$tB]['drawn']++;
                    $standingsMap[$tB]['form'][] = 'D';
                }
            }

            // Update team stats in pcap_teams table
            foreach ($standingsMap as $teamId => &$data) {
                $data['board_points_diff'] = round($data['board_points_for'] - $data['board_points_against'], 1);

                PcapTeam::where('id', $teamId)->update([
                    'wins' => $data['won'],
                    'losses' => $data['lost'],
                    'draws' => $data['drawn'],
                    'match_points' => $data['match_points'],
                    'board_points_for' => $data['board_points_for'],
                    'board_points_against' => $data['board_points_against'],
                ]);
            }
            unset($data);

            // Sort & Rank Alpha Standings
            $alphaStandings = array_values(array_filter($standingsMap, fn($s) => $s['conference'] === 'alpha'));
            usort($alphaStandings, function ($a, $b) {
                if ($b['match_points'] !== $a['match_points']) return $b['match_points'] <=> $a['match_points'];
                if ($b['board_points_diff'] != $a['board_points_diff']) return $b['board_points_diff'] <=> $a['board_points_diff'];
                return $b['board_points_for'] <=> $a['board_points_for'];
            });

            foreach ($alphaStandings as $index => $row) {
                $row['rank'] = $index + 1;
                PcapStanding::create($row);
            }

            // Sort & Rank Omega Standings
            $omegaStandings = array_values(array_filter($standingsMap, fn($s) => $s['conference'] === 'omega'));
            usort($omegaStandings, function ($a, $b) {
                if ($b['match_points'] !== $a['match_points']) return $b['match_points'] <=> $a['match_points'];
                if ($b['board_points_diff'] != $a['board_points_diff']) return $b['board_points_diff'] <=> $a['board_points_diff'];
                return $b['board_points_for'] <=> $a['board_points_for'];
            });

            foreach ($omegaStandings as $index => $row) {
                $row['rank'] = $index + 1;
                PcapStanding::create($row);
            }
        });
    }
}
