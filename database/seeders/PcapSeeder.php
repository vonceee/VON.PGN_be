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
                        ['id' => 'pasig-p1', 'name' => 'Daniel Quizon', 'title' => 'GM', 'rating' => null, 'category' => 'O', 'federation' => 'PHI'],
                        ['id' => 'pasig-p2', 'name' => 'Michael Concio Jr', 'title' => 'IM', 'rating' => null, 'category' => 'O', 'federation' => 'PHI'],
                        ['id' => 'pasig-p3', 'name' => 'Sherily Cua', 'title' => 'WFM', 'rating' => null, 'category' => 'L', 'federation' => 'PHI'],
                        ['id' => 'pasig-p4', 'name' => 'Chito Garma', 'title' => 'IM', 'rating' => null, 'category' => 'S', 'federation' => 'PHI'],
                        ['id' => 'pasig-p5', 'name' => 'Idelfonso Datu', 'title' => 'IM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'pasig-p6', 'name' => 'Marc Kevin Labog', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'pasig-p7', 'name' => 'Jerome Villanueva', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'pasig-p8', 'name' => 'Sherwin Tiu', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'pasig-p9', 'name' => 'Omar Bagalacsa', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'pasig-p10', 'name' => 'Carl Espallardo', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'pasig-p11', 'name' => 'Cromwell Sabado', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'pasig-p12', 'name' => 'Faye Bernardino', 'title' => null, 'rating' => null, 'category' => 'HG/L', 'federation' => 'PHI'],
                        ['id' => 'pasig-p13', 'name' => 'Gombosuren Munkhgal', 'title' => 'GM', 'rating' => null, 'category' => 'O', 'federation' => 'MGL'],
                    ]
                ],
                [
                    'id' => 'san-juan-predators',
                    'name' => 'San Juan Predators',
                    'conference' => 'alpha',
                    'roster' => [
                        ['id' => 'sj-p1', 'name' => 'Jose Aquino Jr.', 'title' => 'NM', 'rating' => null, 'category' => 'O', 'federation' => 'PHI'],
                        ['id' => 'sj-p2', 'name' => 'Karl Viktor Ochoa', 'title' => null, 'rating' => null, 'category' => 'O', 'federation' => 'PHI'],
                        ['id' => 'sj-p3', 'name' => 'Francois Marie Magpily', 'title' => null, 'rating' => null, 'category' => 'L', 'federation' => 'PHI'],
                        ['id' => 'sj-p4', 'name' => 'Joel Anthony Hicap', 'title' => null, 'rating' => null, 'category' => 'S', 'federation' => 'PHI'],
                        ['id' => 'sj-p5', 'name' => 'Rolando Nolte', 'title' => 'IM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'sj-p6', 'name' => 'Narquingden Reyes', 'title' => 'FM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'sj-p7', 'name' => 'Narquingel Reyes', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'sj-p8', 'name' => 'Julius Gonzales', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'sj-p9', 'name' => 'Gavin Lloyd Ong', 'title' => 'AGM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'sj-p10', 'name' => 'Jonathan Jota', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'sj-p11', 'name' => 'Kevin Arquero', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'sj-p12', 'name' => 'Arvie Lozano', 'title' => null, 'rating' => null, 'category' => 'L', 'federation' => 'PHI'],
                        ['id' => 'sj-p13', 'name' => 'Domingo Ramos', 'title' => null, 'rating' => null, 'category' => 'S', 'federation' => 'PHI'],
                        ['id' => 'sj-p14', 'name' => 'Victor Moskalenko', 'title' => 'GM', 'rating' => null, 'category' => 'S', 'federation' => 'ESP'],
                    ]
                ],
                [
                    'id' => 'manila-aq-prime',
                    'name' => 'Manila AQ Prime Assets',
                    'conference' => 'alpha',
                    'roster' => [
                        ['id' => 'maq-p1', 'name' => 'Ellan Asuela', 'title' => 'FM', 'rating' => null, 'category' => 'O', 'federation' => 'PHI'],
                        ['id' => 'maq-p2', 'name' => 'Dale Bernardo', 'title' => 'NM', 'rating' => null, 'category' => 'O', 'federation' => 'PHI'],
                        ['id' => 'maq-p3', 'name' => 'Kylen Joy Mordido', 'title' => 'WIM', 'rating' => null, 'category' => 'L', 'federation' => 'PHI'],
                        ['id' => 'maq-p4', 'name' => 'Ricardo De Guzman', 'title' => 'IM', 'rating' => null, 'category' => 'S', 'federation' => 'PHI'],
                        ['id' => 'maq-p5', 'name' => 'Christian Mark Daluz', 'title' => 'FM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'maq-p6', 'name' => 'Ronald Dableo', 'title' => 'IM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'maq-p7', 'name' => 'Jan Emmanuel Garcia', 'title' => 'IM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'maq-p8', 'name' => 'Ruther Barredo', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'maq-p9', 'name' => 'Adrian Perez', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'maq-p10', 'name' => 'Jovert Valenzuela', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'maq-p11', 'name' => 'Carlos Edgardo Garma', 'title' => 'FM', 'rating' => null, 'category' => 'HG/S', 'federation' => 'PHI'],
                        ['id' => 'maq-p12', 'name' => 'Allaney Jia Doroy', 'title' => 'WFM', 'rating' => null, 'category' => 'HG/L', 'federation' => 'PHI'],
                        ['id' => 'maq-p13', 'name' => 'Paul Sanchez', 'title' => null, 'rating' => null, 'category' => 'O', 'federation' => 'PHI'],
                        ['id' => 'maq-p14', 'name' => 'Mark Paragua', 'title' => 'GM', 'rating' => null, 'category' => 'O', 'federation' => 'PHI'],
                        ['id' => 'maq-p15', 'name' => 'Eric Labog Jr.', 'title' => 'IM', 'rating' => null, 'category' => 'O', 'federation' => 'PHI'],
                        ['id' => 'maq-p16', 'name' => 'Aaryan Varshney', 'title' => 'GM', 'rating' => null, 'category' => 'O', 'federation' => 'IND'],
                    ]
                ],
                [
                    'id' => 'camarines-soaring-eagles',
                    'name' => 'Camarines Soaring Eagles',
                    'conference' => 'alpha',
                    'roster' => [
                        ['id' => 'cam-p1', 'name' => 'Lennon Hart Salgados', 'title' => null, 'rating' => null, 'category' => 'O', 'federation' => 'PHI'],
                        ['id' => 'cam-p2', 'name' => 'Giovanni Mejia', 'title' => 'NM', 'rating' => null, 'category' => 'O', 'federation' => 'PHI'],
                        ['id' => 'cam-p3', 'name' => 'Virgenie Ruaya', 'title' => null, 'rating' => null, 'category' => 'L', 'federation' => 'PHI'],
                        ['id' => 'cam-p4', 'name' => 'Edmundo Gatus', 'title' => 'NM', 'rating' => null, 'category' => 'S', 'federation' => 'PHI'],
                        ['id' => 'cam-p5', 'name' => 'Recarte Tiauson', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'cam-p6', 'name' => 'Jeth Romy Morado', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'cam-p7', 'name' => 'John Marco Balane', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'cam-p8', 'name' => 'Coellier Graspela', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'cam-p9', 'name' => 'Ronald Llavanes', 'title' => 'NM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                    ]
                ],
                [
                    'id' => 'isabela-knights',
                    'name' => 'Isabela Knights of Alexander',
                    'conference' => 'alpha',
                    'roster' => [
                        ['id' => 'isa-p1', 'name' => 'Yves Ranola', 'title' => 'IM', 'rating' => null, 'category' => 'O', 'federation' => 'PHI'],
                        ['id' => 'isa-p2', 'name' => 'Manolito Manaois', 'title' => null, 'rating' => null, 'category' => 'O', 'federation' => 'PHI'],
                        ['id' => 'isa-p3', 'name' => 'Nguyen Thi Mai Hung', 'title' => 'WGM', 'rating' => null, 'category' => 'L', 'federation' => 'VIE'],
                        ['id' => 'isa-p4', 'name' => 'Gerardo Cabellon', 'title' => 'NM', 'rating' => null, 'category' => 'S', 'federation' => 'PHI'],
                        ['id' => 'isa-p5', 'name' => 'Lordwin Espiritu', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'isa-p6', 'name' => 'Melchor Foronda III', 'title' => 'AGM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'isa-p7', 'name' => 'Anwar Cabugatan', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'isa-p8', 'name' => 'Marvin Phua', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'isa-p9', 'name' => 'Romy Fagon', 'title' => 'CM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'isa-p10', 'name' => 'Joseph Merculio Lalas', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'isa-p11', 'name' => 'Francisco Cabe Jr.', 'title' => null, 'rating' => null, 'category' => 'HG/S', 'federation' => 'PHI'],
                        ['id' => 'isa-p12', 'name' => 'Diana Banawa', 'title' => null, 'rating' => null, 'category' => 'L', 'federation' => 'PHI'],
                        ['id' => 'isa-p13', 'name' => 'Angelo Young', 'title' => 'IM', 'rating' => null, 'category' => 'S', 'federation' => 'PHI'],
                    ]
                ],
                [
                    'id' => 'quezon-city-simba',
                    'name' => 'IIEE-PSME Quezon City Simba\'s Tribe',
                    'conference' => 'alpha',
                    'roster' => [
                        ['id' => 'qc-simba-p1', 'name' => 'Steven Breckenridge', 'title' => null, 'rating' => null, 'category' => 'O', 'federation' => 'USA'],
                        ['id' => 'qc-simba-p2', 'name' => 'Rico Salimbagat', 'title' => 'FM', 'rating' => null, 'category' => 'O', 'federation' => 'PHI'],
                        ['id' => 'qc-simba-p3', 'name' => 'Rusela Joya-Magsino', 'title' => 'WNM', 'rating' => null, 'category' => 'L', 'federation' => 'PHI'],
                        ['id' => 'qc-simba-p4', 'name' => 'Leonardo Navarro', 'title' => null, 'rating' => null, 'category' => 'S', 'federation' => 'PHI'],
                        ['id' => 'qc-simba-p5', 'name' => 'Jony Habla', 'title' => 'FM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'qc-simba-p6', 'name' => 'Joseph Navarro', 'title' => 'CM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'qc-simba-p7', 'name' => 'Agapay Apollo', 'title' => 'AGM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'qc-simba-p8', 'name' => 'Francis Talaboc', 'title' => 'AIM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'qc-simba-p9', 'name' => 'Kristian Paulo Cristobal', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'qc-simba-p10', 'name' => 'Norman Madariaga', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'qc-simba-p11', 'name' => 'Freddie Talaboc', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'qc-simba-p12', 'name' => 'Danilo Ponay', 'title' => null, 'rating' => null, 'category' => 'HG/S', 'federation' => 'PHI'],
                        ['id' => 'qc-simba-p13', 'name' => 'Gerald Ferriol', 'title' => 'NM', 'rating' => null, 'category' => 'O', 'federation' => 'PHI'],
                        ['id' => 'qc-simba-p14', 'name' => 'Nicomedes Alisangco', 'title' => 'NM', 'rating' => null, 'category' => 'O', 'federation' => 'PHI'],
                        ['id' => 'qc-simba-p15', 'name' => 'Joseph Galindo', 'title' => 'AGM', 'rating' => null, 'category' => 'O', 'federation' => 'PHI'],
                        ['id' => 'qc-simba-p16', 'name' => 'Jamie Hizon', 'title' => null, 'rating' => null, 'category' => 'L', 'federation' => 'PHI'],
                    ]
                ],
                [
                    'id' => 'ocm-cebu-ninos',
                    'name' => 'OCM Cebu Niños',
                    'conference' => 'alpha',
                    'roster' => [
                        ['id' => 'cebu-p1', 'name' => 'Jeriel Manlimbana', 'title' => null, 'rating' => null, 'category' => 'O', 'federation' => 'PHI'],
                        ['id' => 'cebu-p2', 'name' => 'Jezreel Lopez', 'title' => null, 'rating' => null, 'category' => 'O', 'federation' => 'PHI'],
                        ['id' => 'cebu-p3', 'name' => 'Marian Calimbo', 'title' => null, 'rating' => null, 'category' => 'L', 'federation' => 'PHI'],
                        ['id' => 'cebu-p4', 'name' => 'Eladio Lim III', 'title' => null, 'rating' => null, 'category' => 'S', 'federation' => 'PHI'],
                        ['id' => 'cebu-p5', 'name' => 'Elwin Retanal', 'title' => 'NM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'cebu-p6', 'name' => 'Randy Cabuncal', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'cebu-p7', 'name' => 'Ariel Potot', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                    ]
                ],
                [
                    'id' => 'mindoro-tamaraws',
                    'name' => 'Mindoro Tamaraws',
                    'conference' => 'alpha',
                    'roster' => [
                        ['id' => 'min-p1', 'name' => 'Liu Xiangyi', 'title' => 'IM', 'rating' => null, 'category' => 'O', 'federation' => 'SGP'],
                        ['id' => 'min-p2', 'name' => 'Joselito Asi', 'title' => 'AGM', 'rating' => null, 'category' => 'O', 'federation' => 'PHI'],
                        ['id' => 'min-p3', 'name' => 'Jacqueline Ilao', 'title' => null, 'rating' => null, 'category' => 'L', 'federation' => 'PHI'],
                        ['id' => 'min-p4', 'name' => 'Vicente D. Espolon', 'title' => null, 'rating' => null, 'category' => 'S', 'federation' => 'PHI'],
                        ['id' => 'min-p5', 'name' => 'Ryan Agbunag', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'min-p6', 'name' => 'Julius Joseph De Ramos', 'title' => 'FM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'min-p7', 'name' => 'Nezil Arj Merilles', 'title' => 'AGM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'min-p8', 'name' => 'Rainier Labay', 'title' => 'NM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'min-p9', 'name' => 'Emmanuel Asi', 'title' => 'AGM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'min-p10', 'name' => 'Richard Allen Sicangco', 'title' => 'AIM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'min-p11', 'name' => 'Joel Diaz', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'min-p12', 'name' => 'Adamah Fuentes', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'min-p13', 'name' => 'Alvin Dela Cruz', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'min-p14', 'name' => 'Jefferson Pascua', 'title' => 'AIM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'min-p15', 'name' => 'Cesar Cunanan', 'title' => null, 'rating' => null, 'category' => 'HG/S', 'federation' => 'PHI'],
                        ['id' => 'min-p16', 'name' => 'Cylliz Kaessa Merilles', 'title' => 'AFM', 'rating' => null, 'category' => 'L', 'federation' => 'PHI'],
                    ]
                ],
                [
                    'id' => 'laguna-7lakes',
                    'name' => 'Laguna 7 Lakes',
                    'conference' => 'alpha',
                    'roster' => [
                        ['id' => 'lag-p1', 'name' => 'Alvin Roma', 'title' => 'NM', 'rating' => null, 'category' => 'O', 'federation' => 'PHI'],
                        ['id' => 'lag-p2', 'name' => 'Norvin Gravillo', 'title' => null, 'rating' => null, 'category' => 'O', 'federation' => 'PHI'],
                        ['id' => 'lag-p3', 'name' => 'Jollibee Sidaya', 'title' => null, 'rating' => null, 'category' => 'L', 'federation' => 'PHI'],
                        ['id' => 'lag-p4', 'name' => 'Paul Sumolong', 'title' => null, 'rating' => null, 'category' => 'S', 'federation' => 'PHI'],
                        ['id' => 'lag-p5', 'name' => 'Vince Angelo Medina', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'lag-p6', 'name' => 'Joemarie Villadelgado', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'lag-p7', 'name' => 'Reynaldo Pacia Jr.', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
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
                        ['id' => 'tol-p1', 'name' => 'Rogelio Barcenilla Jr.', 'title' => 'GM', 'rating' => null, 'category' => 'O', 'federation' => 'PHI'],
                        ['id' => 'tol-p2', 'name' => 'Joel Banawa', 'title' => 'IM', 'rating' => null, 'category' => 'O', 'federation' => 'PHI'],
                        ['id' => 'tol-p3', 'name' => 'Cherry Ann Mejia', 'title' => 'WFM', 'rating' => null, 'category' => 'L', 'federation' => 'PHI'],
                        ['id' => 'tol-p4', 'name' => 'Cesar Mariano', 'title' => 'NM', 'rating' => null, 'category' => 'S', 'federation' => 'PHI'],
                        ['id' => 'tol-p5', 'name' => 'Kim Steven Yap', 'title' => 'IM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'tol-p6', 'name' => 'Joel Pimentel', 'title' => 'IM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'tol-p7', 'name' => 'Diego Abraham Capariño', 'title' => 'NM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'tol-p8', 'name' => 'Allan Pason', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'tol-p9', 'name' => 'John Dave Lavandero', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'tol-p10', 'name' => 'Virgen Gil Ruaya', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'tol-p11', 'name' => 'Barlo Nadera', 'title' => 'IM', 'rating' => null, 'category' => 'HG/S', 'federation' => 'PHI'],
                        ['id' => 'tol-p12', 'name' => 'Rico Mascariñas', 'title' => 'IM', 'rating' => null, 'category' => 'HG/S', 'federation' => 'PHI'],
                        ['id' => 'tol-p13', 'name' => 'Cyril Ortega', 'title' => 'NM', 'rating' => null, 'category' => 'HG/S', 'federation' => 'PHI'],
                        ['id' => 'tol-p14', 'name' => 'Bernadette Galas', 'title' => 'WIM', 'rating' => null, 'category' => 'L', 'federation' => 'PHI'],
                        ['id' => 'tol-p15', 'name' => 'Melizah Ruth Carreon', 'title' => 'AGM', 'rating' => null, 'category' => 'L', 'federation' => 'PHI'],
                        ['id' => 'tol-p16', 'name' => 'Enrico Sevillano', 'title' => 'GM', 'rating' => null, 'category' => 'S', 'federation' => 'USA'],
                    ]
                ],
                [
                    'id' => 'manila-load-manna-knights',
                    'name' => 'Manila Load Manna Knights',
                    'conference' => 'omega',
                    'roster' => [
                        ['id' => 'mlm-p1', 'name' => 'Yoseph Taher', 'title' => 'IM', 'rating' => null, 'category' => 'O', 'federation' => 'INA'],
                        ['id' => 'mlm-p2', 'name' => 'Ernie Maraan', 'title' => null, 'rating' => null, 'category' => 'O', 'federation' => 'PHI'],
                        ['id' => 'mlm-p3', 'name' => 'Shania Mae Mendoza', 'title' => 'WFM', 'rating' => null, 'category' => 'L', 'federation' => 'PHI'],
                        ['id' => 'mlm-p4', 'name' => 'Rogelio Antonio', 'title' => 'GM', 'rating' => null, 'category' => 'S', 'federation' => 'PHI'],
                        ['id' => 'mlm-p5', 'name' => 'Paulo Bersamina', 'title' => 'IM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'mlm-p6', 'name' => 'David Elorta', 'title' => 'FM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'mlm-p7', 'name' => 'Nelson Mariano III', 'title' => 'FM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'mlm-p8', 'name' => 'Daryl Samantilla', 'title' => 'NM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'mlm-p9', 'name' => 'Jhulo Goloran', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'mlm-p10', 'name' => 'Genghis Imperial', 'title' => 'CM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'mlm-p11', 'name' => 'Expedito De Leon', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'mlm-p12', 'name' => 'Paulexander Elauria', 'title' => null, 'rating' => null, 'category' => 'S', 'federation' => 'PHI'],
                        ['id' => 'mlm-p13', 'name' => 'Mario Mangubat', 'title' => 'NM', 'rating' => null, 'category' => 'S', 'federation' => 'PHI'],
                        ['id' => 'mlm-p14', 'name' => 'Roldan De Leon', 'title' => null, 'rating' => null, 'category' => 'S', 'federation' => 'PHI'],
                    ]
                ],
                [
                    'id' => 'bacolod-blitzers',
                    'name' => 'Bacolod Blitzers',
                    'conference' => 'omega',
                    'roster' => [
                        ['id' => 'bac-p1', 'name' => 'Felix II Gonzales', 'title' => null, 'rating' => null, 'category' => 'O', 'federation' => 'PHI'],
                        ['id' => 'bac-p2', 'name' => 'Thesius Benitez', 'title' => null, 'rating' => null, 'category' => 'O', 'federation' => 'PHI'],
                        ['id' => 'bac-p3', 'name' => 'Eden Agape Tumbos Ting', 'title' => null, 'rating' => null, 'category' => 'L', 'federation' => 'PHI'],
                        ['id' => 'bac-p4', 'name' => 'Josevito Tapulgo', 'title' => null, 'rating' => null, 'category' => 'S', 'federation' => 'PHI'],
                        ['id' => 'bac-p5', 'name' => 'Edsel Montoya', 'title' => 'NM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'bac-p6', 'name' => 'Ted Ian Montoyo', 'title' => 'NM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'bac-p7', 'name' => 'Ian Cris Henry Udani', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'bac-p8', 'name' => 'Rolando Andador', 'title' => 'NM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'bac-p9', 'name' => 'Romeo Sadia', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'bac-p10', 'name' => 'Eric Abanco', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'bac-p11', 'name' => 'Danny Maersk Mangao', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'bac-p12', 'name' => 'Edwin Tan', 'title' => 'NM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'bac-p13', 'name' => 'Dr. Melben Jochico', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'bac-p14', 'name' => 'Alfred III Acaling', 'title' => null, 'rating' => null, 'category' => 'HG/S', 'federation' => 'PHI'],
                        ['id' => 'bac-p15', 'name' => 'Eduardo Sase', 'title' => null, 'rating' => null, 'category' => 'HG/S', 'federation' => 'PHI'],
                    ]
                ],
                [
                    'id' => 'cagayan-kings',
                    'name' => 'Cagayan Kings',
                    'conference' => 'omega',
                    'roster' => [
                        ['id' => 'cag-p1', 'name' => 'Don Tyrone Delos Santos', 'title' => null, 'rating' => null, 'category' => 'O', 'federation' => 'PHI'],
                        ['id' => 'cag-p2', 'name' => 'Ali Guya', 'title' => null, 'rating' => null, 'category' => 'O', 'federation' => 'PHI'],
                        ['id' => 'cag-p3', 'name' => 'April Joy Ramos', 'title' => null, 'rating' => null, 'category' => 'L', 'federation' => 'PHI'],
                        ['id' => 'cag-p4', 'name' => 'Gary-Legaspi', 'title' => null, 'rating' => null, 'category' => 'S', 'federation' => 'PHI'],
                        ['id' => 'cag-p5', 'name' => 'Jake Tumaliuan', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'cag-p6', 'name' => 'Marc Francis Balanay', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'cag-p7', 'name' => 'Bencelie Fernandez', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'cag-p8', 'name' => 'Alexander Jude S. Malabad', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'cag-p9', 'name' => 'Marfred Sanchez', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'cag-p10', 'name' => 'Joey Cabaya', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'cag-p11', 'name' => 'Robert James Pe Benito', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'cag-p12', 'name' => 'John Robert Bumatay', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'cag-p13', 'name' => 'Laurence Wilfred Dumadag', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'cag-p14', 'name' => 'Jose Jude Antonio Ulanday', 'title' => null, 'rating' => null, 'category' => 'S', 'federation' => 'PHI'],
                        ['id' => 'cag-p15', 'name' => 'Harison Maamo', 'title' => null, 'rating' => null, 'category' => 'O', 'federation' => 'PHI'],
                        ['id' => 'cag-p16', 'name' => 'Alexei Barsov', 'title' => null, 'rating' => null, 'category' => 'S', 'federation' => 'UZB'],
                    ]
                ],
                [
                    'id' => 'cavite-spartans',
                    'name' => 'Cavite Spartans',
                    'conference' => 'omega',
                    'roster' => [
                        ['id' => 'cav-p1', 'name' => 'Jim Dean', 'title' => 'FM', 'rating' => null, 'category' => 'O', 'federation' => 'USA'],
                        ['id' => 'cav-p2', 'name' => 'Cathrino Pestaño', 'title' => 'NM', 'rating' => null, 'category' => 'O', 'federation' => 'PHI'],
                        ['id' => 'cav-p3', 'name' => 'Jessica Aguilar', 'title' => null, 'rating' => null, 'category' => 'L', 'federation' => 'PHI'],
                        ['id' => 'cav-p4', 'name' => 'Dioniver Medrano', 'title' => null, 'rating' => null, 'category' => 'S', 'federation' => 'PHI'],
                        ['id' => 'cav-p5', 'name' => 'Voltaire Marc Paraguya', 'title' => 'AGM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'cav-p6', 'name' => 'Jayson Visca', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'cav-p7', 'name' => 'Marco Jay Mabasa', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'cav-p8', 'name' => 'Renie Malupa', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'cav-p9', 'name' => 'Jeffrey Romera', 'title' => 'AIM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'cav-p10', 'name' => 'Aldrin Pasno', 'title' => 'AIM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'cav-p11', 'name' => 'Albert Pasno', 'title' => 'AFM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'cav-p12', 'name' => 'Jayson Danday', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'cav-p13', 'name' => 'Rodolf Perez', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'cav-p14', 'name' => 'Robvy Jemuel Wong', 'title' => 'AIM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'cav-p15', 'name' => 'Christine Muli', 'title' => 'AIM', 'rating' => null, 'category' => 'HG/L', 'federation' => 'PHI'],
                        ['id' => 'cav-p16', 'name' => 'Eduardo Tunguia', 'title' => 'AFM', 'rating' => null, 'category' => 'HG/S', 'federation' => 'PHI'],
                    ]
                ],
                [
                    'id' => 'arriba-iriga-oragons',
                    'name' => 'Arriba Iriga Oragons',
                    'conference' => 'omega',
                    'roster' => [
                        ['id' => 'iri-p1', 'name' => 'Alji Cantonjos', 'title' => null, 'rating' => null, 'category' => 'O', 'federation' => 'PHI'],
                        ['id' => 'iri-p2', 'name' => 'Vladimir Gonzales', 'title' => null, 'rating' => null, 'category' => 'O', 'federation' => 'PHI'],
                        ['id' => 'iri-p3', 'name' => 'Isabel Palibino', 'title' => null, 'rating' => null, 'category' => 'L', 'federation' => 'PHI'],
                        ['id' => 'iri-p4', 'name' => 'Roger Pesimo', 'title' => null, 'rating' => null, 'category' => 'S', 'federation' => 'PHI'],
                        ['id' => 'iri-p5', 'name' => 'Joeven Polsotin', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'iri-p6', 'name' => 'Fr. Emil Valeza', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'iri-p7', 'name' => 'Glennen Artuz', 'title' => 'NM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'iri-p8', 'name' => 'Jeffrey Vegas', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'iri-p9', 'name' => 'Jayvee Relleve', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'iri-p10', 'name' => 'Paul Christian Barroga', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'iri-p11', 'name' => 'Johnlyn Buenaventura', 'title' => null, 'rating' => null, 'category' => 'HG/L', 'federation' => 'PHI'],
                        ['id' => 'iri-p12', 'name' => 'Samantha Glo Revita-Formoso', 'title' => null, 'rating' => null, 'category' => 'HG/L', 'federation' => 'PHI'],
                        ['id' => 'iri-p13', 'name' => 'Veron Camposano', 'title' => null, 'rating' => null, 'category' => 'HG/L', 'federation' => 'PHI'],
                        ['id' => 'iri-p14', 'name' => 'Eldin Eroma', 'title' => null, 'rating' => null, 'category' => 'O', 'federation' => 'PHI'],
                    ]
                ],
                [
                    'id' => 'rizal-batch-towers',
                    'name' => 'Rizal Batch Towers',
                    'conference' => 'omega',
                    'roster' => [
                        ['id' => 'riz-p1', 'name' => 'Raymond Salcedo', 'title' => 'NM', 'rating' => null, 'category' => 'O', 'federation' => 'PHI'],
                        ['id' => 'riz-p2', 'name' => 'Carlo Magno Rosaupan', 'title' => 'NM', 'rating' => null, 'category' => 'O', 'federation' => 'PHI'],
                        ['id' => 'riz-p3', 'name' => 'Irene Rivera', 'title' => null, 'rating' => null, 'category' => 'L', 'federation' => 'PHI'],
                        ['id' => 'riz-p4', 'name' => 'Henry Villanueva', 'title' => null, 'rating' => null, 'category' => 'S', 'federation' => 'PHI'],
                        ['id' => 'riz-p5', 'name' => 'Givy M. Bartolome', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'riz-p6', 'name' => 'Walt Alen Talan', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'riz-p7', 'name' => 'Marlon Constantino', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'riz-p8', 'name' => 'Leo Anthony Rabulan', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'riz-p9', 'name' => 'Sonny B Dela Rosa', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'riz-p10', 'name' => 'Eric Oandra', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'riz-p11', 'name' => 'Renato Cruz Jr.', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'riz-p12', 'name' => 'John Perzeus S. Orozco', 'title' => null, 'rating' => null, 'category' => 'O', 'federation' => 'PHI'],
                        ['id' => 'riz-p13', 'name' => 'Jamaica Marie Lagrio', 'title' => null, 'rating' => null, 'category' => 'L', 'federation' => 'PHI'],
                    ]
                ],
                [
                    'id' => 'zamboanga-sultans',
                    'name' => 'Zamboanga Sultans',
                    'conference' => 'omega',
                    'roster' => [
                        ['id' => 'zam-p1', 'name' => 'Jones Maghuyop', 'title' => null, 'rating' => null, 'category' => 'O', 'federation' => 'PHI'],
                        ['id' => 'zam-p2', 'name' => 'Jordan Gadayan', 'title' => null, 'rating' => null, 'category' => 'O', 'federation' => 'PHI'],
                        ['id' => 'zam-p3', 'name' => 'Sarah Mae Chua', 'title' => null, 'rating' => null, 'category' => 'L', 'federation' => 'PHI'],
                        ['id' => 'zam-p4', 'name' => 'Francisco Delos Santos', 'title' => null, 'rating' => null, 'category' => 'S', 'federation' => 'PHI'],
                        ['id' => 'zam-p5', 'name' => 'Robick Vohn Villa', 'title' => 'NM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'zam-p6', 'name' => 'Abdulhan Agga', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'zam-p7', 'name' => 'Faizal Najar', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'zam-p8', 'name' => 'Rey Reyes', 'title' => 'AGM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'zam-p9', 'name' => 'Zulfikar Sali', 'title' => 'NM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'zam-p10', 'name' => 'Rodrigo Villa Jr.', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'zam-p11', 'name' => 'Engr. Nathan Cornella', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'zam-p12', 'name' => 'Atty Anthony Orbe', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'zam-p13', 'name' => 'Sarri Subahani', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'zam-p14', 'name' => 'Dr Saibzur Edding', 'title' => null, 'rating' => null, 'category' => 'HG/S', 'federation' => 'PHI'],
                    ]
                ],
                [
                    'id' => 'qc-chessmates-stallions',
                    'name' => 'QC - Chessmates Stallions',
                    'conference' => 'omega',
                    'roster' => [
                        ['id' => 'qc-stal-p1', 'name' => 'Andrew Elpedes', 'title' => null, 'rating' => null, 'category' => 'O', 'federation' => 'PHI'],
                        ['id' => 'qc-stal-p2', 'name' => 'Robert Cacho', 'title' => null, 'rating' => null, 'category' => 'O', 'federation' => 'PHI'],
                        ['id' => 'qc-stal-p3', 'name' => 'Robelle De Jesus', 'title' => null, 'rating' => null, 'category' => 'L', 'federation' => 'PHI'],
                        ['id' => 'qc-stal-p4', 'name' => 'Bernardo Yap', 'title' => null, 'rating' => null, 'category' => 'S', 'federation' => 'PHI'],
                        ['id' => 'qc-stal-p5', 'name' => 'Arthur Macaspac', 'title' => 'NM', 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'qc-stal-p6', 'name' => 'Adriel Nicolas Macaspac', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'qc-stal-p7', 'name' => 'Matthew Gotel', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'qc-stal-p8', 'name' => 'Marcus Fratkin', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'qc-stal-p9', 'name' => 'Romelito Lucion', 'title' => null, 'rating' => null, 'category' => 'HG', 'federation' => 'PHI'],
                        ['id' => 'qc-stal-p10', 'name' => 'Rodel Juadines', 'title' => null, 'rating' => null, 'category' => 'S', 'federation' => 'PHI'],
                        ['id' => 'qc-stal-p11', 'name' => 'Stephen Manzanero', 'title' => null, 'rating' => null, 'category' => 'O', 'federation' => 'PHI'],
                        ['id' => 'qc-stal-p12', 'name' => 'John Lee Antonio', 'title' => null, 'rating' => null, 'category' => 'O', 'federation' => 'PHI'],
                        ['id' => 'qc-stal-p13', 'name' => 'Mervin Lumidao', 'title' => null, 'rating' => null, 'category' => 'O', 'federation' => 'PHI'],
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

            // 2. STANDINGS DATA (Clean zeroed-out regular season standings)
            $alphaRank = 1;
            $omegaRank = 1;
            foreach ($teamsData as $teamItem) {
                $isAlpha = $teamItem['conference'] === 'alpha';
                PcapStanding::create([
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
                    'rank' => $isAlpha ? $alphaRank++ : $omegaRank++,
                ]);
            }

            // 3. MATCHDAY FIXTURES (Empty - ready for legitimate fixture creation in PCAP Admin)
        });
    }
}
