<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Chapter;
use App\Models\Lesson;

class OpeningPrinciplesSeeder extends Seeder
{
    public function run(): void
    {
        $course = Course::updateOrCreate(
            ['slug' => 'opening-principles'],
            [
                'title' => 'Opening Principles',
                'description' => '
                    <p>You know how every piece moves. You can set up the board, castle, promote a pawn, and recognize checkmate. You are ready for the next question — what should you actually do at the start of a game?</p>
                    <br>
                    <p>The opening is the most important phase of chess. The decisions you make in the first ten moves shape everything that comes after. Strong players do not guess their way through the opening — they follow a set of principles that have been tested over centuries of play.</p>
                    <br>
                    <p>In this course, you will learn those principles. Not as a list of rules to memorize, but as ideas that make sense — so that when you sit down at a board, you know exactly what you are trying to do and why.</p>
                    <br>
                    <p>By the end, you will have a clear plan for every opening of every game you play.</p>
                ',
            ]
        );

        /*
        ================================
        CHAPTER 1 — WHAT IS THE OPENING?
        ================================

        Purpose: Before teaching any principle, students need to understand what
        the opening phase is, when it ends, and what it is trying to achieve.
        Without this foundation, the five principles feel like arbitrary rules
        rather than tools in service of a goal.

        LESSON BREAKDOWN:
        Lesson 1 — The Three Phases   : Opening, middlegame, endgame — what each one is
        Lesson 2 — The Goal           : What you are trying to accomplish in the opening
        ================================
        */

        $chapter1 = Chapter::updateOrCreate(
            ['course_id' => $course->id, 'order' => 1],
            ['title' => 'What Is the Opening?']
        );

        /*
        ================================
        LESSON 1 — THE THREE PHASES
        ================================

        COACHING STRUCTURE:
        1. Hook      — Chess is not one game; it is three games played back to back
        2. Opening   — What it is, roughly how long it lasts, what defines it
        3. Middlegame — What it is, how it feels different
        4. Endgame   — What it is, why the king becomes active
        5. See it    — Board showing the transition from opening to middlegame
        6. Coach tip — Winning the opening does not win the game, but losing it makes everything harder
        ================================
        */

        Lesson::updateOrCreate(
            ['slug' => 'the-three-phases'],
            [
                'chapter_id' => $chapter1->id,
                'title' => 'The Three Phases of Chess',
                'order' => 1,
                                'content_blocks' => [
                    [
                        'type' => 'text',
                        'content' => '
                            <p>Every chess game has three phases. They do not have sharp boundaries — one flows into the next — but every game passes through all three, and each one requires a different way of thinking.</p>
                            <br>
                            <p><strong>The Opening</strong> is the start of the game — roughly the first ten to fifteen moves. Both players are getting their pieces off the back rank and into active positions. Nobody is attacking yet. The opening is about preparation: building a position from which you can fight effectively.</p>
                            <br>
                            <p><strong>The Middlegame</strong> begins once both players have developed their pieces and castled. This is where the real fighting happens — attacks, sacrifices, tactics, and plans. The middlegame is the most complex phase and the hardest to study, which is exactly why a good opening matters. A strong opening gives you a fighting chance in the middlegame. A weak one puts you on the back foot before the battle has even started.</p>
                            <br>
                            <p><strong>The Endgame</strong> arrives when most of the pieces have been exchanged and only a few remain. The king, which spent the whole game hiding, now becomes an active and powerful piece. Endgames are about precision — pushing pawns, outmaneuvering the opponent\'s king, and converting small advantages into wins.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/IoDYlfZK/DF2IL7J4',
                            'instructions' => 'This game shows a game moving through all three phases. Watch how the board looks in the opening — pieces on the back rank, pawns moving forward. Then notice how it changes as pieces develop and the fighting begins. Finally, see how the endgame strips the board down to just a few pieces.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p><strong>Coach\'s tip:</strong> Winning the opening does not win the game — but losing the opening makes everything that follows much harder. Think of the opening as building a house. You will not live in the foundation, but if the foundation is weak, the house will not stand. The principles you are about to learn are how you build a solid foundation, every single game.</p>
                        '
                    ],
                ]
            ]
        );

        /*
        ================================
        LESSON 2 — THE GOAL OF THE OPENING
        ================================

        COACHING STRUCTURE:
        1. Hook      — Most beginners move pieces without a plan; this lesson gives them one
        2. The goal  — Three things every good opening achieves: control, development, safety
        3. See it    — Board contrasting a good opening and a bad one side by side
        4. Practice  — Board: identify which position came from good opening play and why
        5. Coach tip — Every move in the opening should serve at least one of these three goals
        ================================
        */

        Lesson::updateOrCreate(
            [
                'slug' => 'the-goal-of-the-opening',
                'chapter_id' => $chapter1->id,
                'title' => 'The Goal of the Opening',
                'order' => 2,
                                'content_blocks' => [
                    [
                        'type' => 'text',
                        'content' => '
                            <p>Most beginners move pieces in the opening without a clear plan. They advance a pawn here, move a knight there, maybe bring the queen out early because she is powerful. By move ten, their pieces are scattered, their king is still in the center, and they are already in trouble — without knowing why.</p>
                            <br>
                            <p>The goal of the opening can be summed up in three things. Every strong player is working toward all three at once:</p>
                            <br>
                            <p><strong>1. Control the center.</strong> The four central squares — e4, d4, e5, d5 — are the most valuable squares on the board. Pieces in or near the center have more options, more range, and more power. The fight for the center begins on move one.</p>
                            <br>
                            <p><strong>2. Develop your pieces.</strong> Development means getting your pieces off the back rank and into active positions where they can participate in the game. A piece sitting on its starting square is doing nothing. Every move in the opening should bring another piece into the game.</p>
                            <br>
                            <p><strong>3. Keep your king safe.</strong> The king starts the game in the center — the most dangerous place to be once the position opens up. Getting the king safely castled, tucked away in the corner, is one of the most important goals of the opening.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/IoDYlfZK/ApJAAJeb',
                            'instructions' => 'Compare these two positions — both after ten moves. One player followed good opening principles: pieces developed, center controlled, king castled. The other made common beginner mistakes: pieces undeveloped, queen moved too early, king still in the center. Which position would you rather play?',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p><strong>Coach\'s tip:</strong> Before every move in the opening, ask yourself one question — does this move help me control the center, develop a piece, or keep my king safe? If the answer is no to all three, think again. The opening is not the time for experiments. It is the time to build.',
                    ]
                ],
            ]
        );

        /*
        ================================
        CHAPTER 2 — THE FIVE PRINCIPLES
        ================================

        Each principle gets its own lesson, taught in dependency order:
        1. Control the Center          — the foundation everything else rests on
        2. Develop Your Pieces         — how to execute center control efficiently
        3. Avoid Moving the Same Piece Twice — the cost of poor development
        4. Do Not Bring the Queen Out Early  — why the most powerful piece needs to wait
        5. Castle Early                — the culmination of good opening play

        ================================
        */

        $chapter2 = Chapter::updateOrCreate(
            ['course_id' => $course->id, 'order' => 2],
            ['title' => 'The Five Principles']
        );

        /*
        ================================
        LESSON 1 — CONTROL THE CENTER
        ================================

        COACHING STRUCTURE:
        1. Hook      — Why the center? Frame it with a battlefield analogy
        2. The four  — Identify the four central squares: e4, d4, e5, d5
        3. Why it    — Pieces in the center have more options; demonstrate with a knight
        4. See it    — Board: knight on e4 vs knight on a1 — count reachable squares
        5. How to    — Two ways to control the center: occupy it or aim at it with pawns and pieces
        6. See it    — Board: e4 and d4 pawn center vs fianchetto bishop aiming at center
        7. Practice  — Board: choose the move that best controls the center
        8. Coach tip — The player who controls the center controls the game
        ================================
        */

        Lesson::updateOrCreate(
            ['slug' => 'control-the-center'],
            [
                'chapter_id' => $chapter2->id,
                'title' => 'Control the Center',
                'order' => 1,
               
                'content_blocks' => [
                    [
                        'type' => 'text',
                        'content' => '
                            <p>Think of a chess game as a battle. Like any battle, the side that controls the high ground has the advantage — they can see further, move faster, and attack in more directions.</p>
                            <br>
                            <p>In chess, the high ground is the center of the board.</p>
                            <br>
                            <p>The four squares at the heart of the board — <strong>e4, d4, e5, and d5</strong> — are the most valuable real estate in chess. They are the squares every piece wants to occupy or influence. And from move one, both players are fighting over them.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/IoDYlfZK/cmjVamfb',
                            'instructions' => 'The four highlighted squares are e4, d4, e5, and d5 — the center of the board. These are the squares that matter most in the opening. Every principle you learn in this course connects back to controlling these four squares.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p>Here is why the center is so powerful. A piece in the center has more options than a piece on the edge.</p>
                            <br>
                            <p>Take the knight as an example. From the center of the board, a knight can reach up to <strong>8 squares</strong>. From a corner, it can only reach <strong>2</strong>. The same piece, the same rules — but one position is four times more powerful than the other, simply because of where it stands.</p>
                            <br>
                            <p>This is true for every piece. A bishop cutting across the center controls far more squares than a bishop trapped behind its own pawns on the edge. A rook on an open central file dominates the board. The center multiplies the power of everything on it.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/IoDYlfZK/Y8ULQKnC',
                            'instructions' => 'Two knights — one on d5, one on h8. Click each knight and count how many squares it can move to. The knight in the center reaches 8 squares. The knight in the corner reaches only 2. Same piece, completely different power. This is why the center matters.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p>There are two ways to control the center:</p>
                            <br>
                            <p><strong>Occupy it</strong> — place your pawns or pieces directly on the central squares. Moving a pawn to e4 or d4 on your first move is the most direct way to claim the center immediately.</p>
                            <br>
                            <p><strong>Aim at it</strong> — point your pieces toward the center without occupying it directly. A bishop developed to b2 or g2 does not sit on a central square, but it fires across the entire center diagonal. This is a more flexible approach used by advanced players.</p>
                            <br>
                            <p>For now, focus on the first method. Moving your central pawns forward on move one is the clearest, most direct way to start the fight for the center.</p>
                        '
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p><strong>Coach\'s tip:</strong> The player who controls the center controls the game. It sounds simple, but it is one of the most profound truths in chess. When you are unsure what to do in the opening, ask yourself: does this move help me control the center? That question will never steer you wrong.</p>
                        '
                    ],
                ]
            ]
        );

        /*
        ================================
        LESSON 2 — DEVELOP YOUR PIECES
        ================================

        COACHING STRUCTURE:
        1. Hook      — Center control is the goal; development is how you get there
        2. What it   — Development defined: getting pieces off the back rank into active play
        3. Order     — Which pieces to develop first and why (knights before bishops, minor before major)
        4. See it    — Board showing a well-developed position after 5 moves
        5. The cost  — What an undeveloped position looks like and why it loses
        6. See it    — Board: developed vs undeveloped position; count active pieces
        7. Practice  — Board: choose the developing move from three options
        8. Coach tip — Every move that does not develop a piece in the opening is a move wasted
        ================================
        */

        Lesson::updateOrCreate(
            ['slug' => 'develop-your-pieces'],
            [
                'chapter_id' => $chapter2->id,
                'title' => 'Develop Your Pieces',
                'order' => 2,
               
                'content_blocks' => [
                    [
                        'type' => 'text',
                        'content' => '
                            <p>Controlling the center is the goal of the opening. But you cannot control the center with just your pawns. You need your pieces — and right now, they are all sitting on the back rank, doing nothing.</p>
                            <br>
                            <p>Getting your pieces off the back rank and into active positions is called <strong>development</strong>. It is one of the most important habits in chess, and it is the engine that drives everything else in the opening.</p>
                            <br>
                            <p>A piece that has not moved is a piece that is not helping you. Imagine going into a fight with half your team sitting on the bench. That is what an undeveloped position feels like.</p>
                        '
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p>Which pieces should you develop first?</p>
                            <br>
                            <p><strong>Knights and bishops first.</strong> These are called minor pieces, and they are the fastest to develop — they can come out in one move and immediately influence the center. As a general rule, develop your knights before your bishops, because the best squares for knights are usually clearer at the start of the game.</p>
                            <br>
                            <p><strong>Rooks and queens later.</strong> Rooks need open files to be useful — files that are not yet open in the early game. The queen needs to wait for her own reasons, which you will learn in the next lesson. For now, focus on getting the minor pieces out first.</p>
                            <br>
                            <p>The ideal goal for the opening is simple: <strong>develop a new piece on every move</strong>, until all your minor pieces are active and your king is castled.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/IoDYlfZK/WhreFWOp',
                            'instructions' => 'Watch this sequence of five moves. Every single move develops a new piece or fights for the center. By move five, both knights are out, both bishops are active, and the position is ready to castle. This is what efficient development looks like.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p>Now compare that to what happens when a player ignores development.</p>
                            <br>
                            <p>After five moves, if you have only moved pawns and shuffled one piece around, your opponent who has developed all their pieces has a significant advantage — not because of any brilliant tactics, but simply because more of their army is ready to fight.</p>
                        '
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p><strong>Coach\'s tip:</strong> Every move in the opening that does not develop a piece is a move given to your opponent. They develop, you do not — and after ten moves, they have a full army ready to fight while yours is still warming up. Development is not exciting. It does not win the game on its own. But it makes everything else possible.</p>
                        '
                    ],
                ]
            ]
        );

        /*
        ================================
        LESSON 3 — AVOID MOVING THE SAME PIECE TWICE
        ================================

        COACHING STRUCTURE:
        1. Hook      — Frame it as wasted time: in chess, time is a resource
        2. The idea  — Moving the same piece twice means another piece sits undeveloped
        3. The cost  — Quantify the loss: one extra move = opponent gets one free developing move
        4. See it    — Board: sequence where one side moves a piece twice; other side develops freely
        5. Exception — When is it acceptable to move the same piece twice?
        6. See it    — Board: example of justified piece relocation (forced or winning material)
        7. Practice  — Board: choose between developing a new piece and moving one already moved
        8. Coach tip — Ask "is there a new piece I can develop?" before moving the same piece again
        ================================
        */

        Lesson::updateOrCreate(
            ['slug' => 'avoid-moving-same-piece-twice'],
            [
                'chapter_id' => $chapter2->id,
                'title' => 'Avoid Moving the Same Piece Twice',
                'order' => 3,
               
                'content_blocks' => [
                    [
                        'type' => 'text',
                        'content' => '
                            <p>In chess, time is a resource — just like material. Every move is a unit of time, and in the opening, both players are racing to get their pieces ready before the fighting starts. Wasting time puts you behind.</p>
                            <br>
                            <p>One of the most common ways beginners waste time is by <strong>moving the same piece twice in the opening</strong>. It feels harmless — maybe you want to reposition a knight to a better square, or you moved a bishop and now you want to move it again. But every time you move a piece that has already moved, you are spending a unit of time that could have been used to develop a piece that is still sitting idle.</p>
                            <br>
                            <p>Think of it this way: if you move the same knight twice while your opponent develops a new piece each move, after four moves you have two pieces active and they have four. Your entire army is half the size of theirs — and the game has barely started.</p>
                        '
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p>That said, there are times when moving the same piece twice is the right decision. The principle is not an absolute rule — it is a reminder to think carefully before you do it.</p>
                            <br>
                            <p>Moving the same piece twice is acceptable when:</p>
                            <br>
                            <ul>
                                <li><strong>You are forced to.</strong> If your piece is under attack and staying put means losing it, you have to move it.</li><br>
                                <li><strong>You win material.</strong> If moving a piece a second time allows you to capture an enemy piece for free, the material gain is worth the time spent.</li><br>
                                <li><strong>The position demands it.</strong> Sometimes the best square for a piece is not reachable in one move. An experienced player knows when a repositioning is worth the tempo.</li>
                            </ul>
                            <br>
                            <p>In all other cases — especially early in the opening — the default answer is: develop a new piece instead.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/IoDYlfZK/aTHIMHWB',
                            'instructions' => 'Watch this sequence. White moves the same knight three times in the first six moves, chasing small advantages that do not matter yet. Black develops a new piece every single move. By move six, count the developed pieces on each side. White\'s time-wasting has given Black a free lead in development.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p><strong>Coach\'s tip:</strong> Before you move a piece that has already moved, ask yourself one question — is there an undeveloped piece I could bring into the game instead? Most of the time, the answer is yes, and that is the move you should make. Save the repositioning for later, when the opening is over and the position calls for it.</p>
                        '
                    ],
                ]
            ]
        );

        /*
        ================================
        LESSON 4 — DO NOT BRING THE QUEEN OUT TOO EARLY
        ================================

        COACHING STRUCTURE:
        1. Hook      — The queen is the most powerful piece; so why does she need to wait?
        2. The trap  — What happens when the queen comes out early (gets chased)
        3. See it    — Board: Scholar's Mate attempt — queen out early, then refuted
        4. The cost  — Every time the queen is chased, the opponent develops a piece for free
        5. The rule  — Queen comes out after minor pieces are developed and king is safe
        6. Exception — When early queen moves are fine (recaptures, specific positions)
        7. Practice  — Board: queen is tempted to come out; find the developing move instead
        8. Coach tip — The queen is most dangerous when the opponent cannot chase her; earn that right first
        ================================
        */

        Lesson::updateOrCreate(
            ['slug' => 'queen-not-too-early'],
            [
                'chapter_id' => $chapter2->id,
                'title' => "Don't Bring the Queen Out Too Early",
                'order' => 4,
               
                'content_blocks' => [
                    [
                        'type' => 'text',
                        'content' => '
                            <p>The queen is the most powerful piece on the board. So it seems logical to bring her out early and start attacking immediately, right?</p>
                            <br>
                            <p>This is one of the most common mistakes in chess — and it is a trap that catches beginners at every level. Here is why it backfires.</p>
                            <br>
                            <p>The queen is so valuable that your opponent can never afford to let her be captured. The moment she appears on the board, every enemy piece becomes a potential threat to her. And every time your opponent threatens the queen and forces her to move, they get to develop a piece — for free. They are building their army while you are running away with your most important piece.</p>
                            <br>
                            <p>Let us see how this plays out.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/IoDYlfZK/DxftabYM',
                            'instructions' => 'White brings the queen out on move two, looking for quick attacks. Watch what happens — Black simply develops pieces and threatens the queen on every move. White spends the entire opening retreating. By move eight, Black has a fully developed army and White has achieved nothing. Notice how Black gains a free developing move every time the queen is chased.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p>The lesson is clear: an early queen is not a weapon — it is a liability. She gets chased around the board by pieces worth far less than she is, and every chase costs you a developing move.</p>
                            <br>
                            <p>The right time to bring the queen out is after your minor pieces are developed and your king is safely castled. At that point, your opponent\'s pieces are also developed, and they cannot chase the queen with tempo anymore — they have run out of free developing moves. The queen becomes dangerous precisely because there is nowhere convenient to chase her.</p>
                            <br>
                            <p>There are exceptions. Moving the queen to recapture a piece is fine — you are not bringing her out aggressively, you are restoring material balance. And in some specific positions, an early queen move is part of a well-known idea. But those are exceptions to learn later. For now, the default is clear: develop your minor pieces first.</p>
                        '
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p><strong>Coach\'s tip:</strong> The queen is most dangerous when your opponent cannot conveniently chase her. That moment arrives naturally once your pieces are developed and your king is safe. Earn the right to bring the queen out — do not rush it. A queen that comes out at the right time wins games. A queen that comes out too early loses them.</p>
                        '
                    ],
                ]
            ]
        );

        /*
        ================================
        LESSON 5 — CASTLE EARLY
        ================================

        COACHING STRUCTURE:
        1. Hook      — Tie together everything learned so far: development leads here
        2. Why       — The king in the center is a target on open files and diagonals
        3. See it    — Board: king stuck in center, attacked down an open file
        4. The goal  — Castle within the first ten moves; connect the rooks
        5. Which     — Kingside vs queenside castling: which to choose and when
        6. See it    — Board: both sides castle kingside; compare king safety
        7. Bonus     — Castling activates the rook; two benefits in one move
        8. Practice  — Board: identify the correct move sequence to castle as quickly as possible
        9. Coach tip — An uncastled king is an invitation to attack
        ================================
        */

        Lesson::updateOrCreate(
            ['slug' => 'castle-early'],
            [
                'chapter_id' => $chapter2->id,
                'title' => 'Castle Early',
                'order' => 5,
               
                'content_blocks' => [
                    [
                        'type' => 'text',
                        'content' => '
                            <p>Look at everything you have learned so far. You control the center. You develop your pieces efficiently. You do not waste time moving the same piece twice. You keep the queen back until the position is ready.</p>
                            <br>
                            <p>There is one more thing that completes a strong opening — getting your king to safety.</p>
                            <br>
                            <p>At the start of the game, the king sits on e1 — right in the center of the board. As pawns move and files open up, that central king becomes increasingly exposed. A rook on an open e-file, a bishop aimed at the center, a queen bearing down the d-file — all of these become direct threats to a king that never castled.</p>
                        '
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p>Castling solves this. It moves the king to the corner — behind a wall of pawns, far from the center of the action — and at the same time brings a rook toward the middle of the board where it can be useful.</p>
                            <br>
                            <p>Two benefits in a single move. That is why castling early is one of the most efficient things you can do in the opening.</p>
                            <br>
                            <p>Which direction should you castle? In most games, <strong>kingside castling</strong> is the safer and faster choice. The king ends up further from the center, protected by three pawns that are usually still on their starting squares. Queenside castling is more aggressive — the king is slightly more exposed, but the rook becomes active very quickly. For now, default to kingside unless you have a clear reason to go the other way.</p>
                            <br>
                            <p>The goal: <strong>castle within your first ten moves</strong>. Ideally sooner. The faster your king is safe, the more freely you can attack without worrying about a counter-attack on your own king.</p>
                        '
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p><strong>Coach\'s tip:</strong> An uncastled king is an open invitation to your opponent. The moment they see your king stuck in the center, every strong player will look for a way to open the position and attack it. Do not give them that opportunity. Castle early, put the king away, and then focus on your own plans.</p>
                        '
                    ],
                ]
            ]
        );

        /*
        ================================
        CHAPTER 3 — PUTTING IT TOGETHER
        ================================

        Purpose: The five principles have been taught individually. Now students
        need to see them working as a connected system — not five separate rules,
        but one coherent approach to the opening. This chapter shows all five
        principles in a single model game, then closes the course.

        LESSON BREAKDOWN:
        Lesson 1 — A Model Opening    : All five principles applied move by move
        Lesson 2 — You Are Ready      : Course summary and what comes next
        ================================
        */

        $chapter3 = Chapter::updateOrCreate(
            ['course_id' => $course->id, 'order' => 3],
            ['title' => 'Putting It Together']
        );

        /*
        ================================
        LESSON 1 — A MODEL OPENING
        ================================

        COACHING STRUCTURE:
        1. Hook      — The principles only matter if they work together; let us see them in action
        2. Move 1    — e4: fights for the center immediately
        3. Move 2    — Nf3: develops a piece, attacks the center, does not block the bishop
        4. Move 3    — Bc4: develops the bishop toward the center, prepares to castle
        5. Move 4    — O-O: castles; king is safe, rook is connected
        6. Move 5+   — Complete development; queen comes out only when position is ready
        7. Full board — See the completed position and identify each principle at work
        8. Practice  — Board: play the first five moves from memory, following the principles
        9. Coach tip — You do not need to memorize openings; you need to understand these principles
        ================================
        */

        Lesson::updateOrCreate(
            ['slug' => 'a-model-opening'],
            [
                'chapter_id' => $chapter3->id,
                'title' => 'A Model Opening',
                'order' => 1,
               
                'content_blocks' => [
                    [
                        'type' => 'text',
                        'content' => '
                            <p>You have learned five principles. Now let us watch them work together in a real opening.</p>
                            <br>
                            <p>What follows is not a specific opening you need to memorize. It is a model — a demonstration of what good opening play looks like when all five principles are applied at once, move by move.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/IoDYlfZK/bb1Z3Izn',
                            'instructions' => 'Follow this opening move by move. For each move, read the annotation — it explains which principle that move is following and why. By move eight, the position demonstrates all five principles working together: center control, full development, no piece moved twice, queen kept back until the right moment, and king safely castled.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p>Let us break down what each move accomplished:</p>
                            <br>
                            <ul>
                                <li><strong>Move 1 — e4:</strong> Occupies the center immediately and opens lines for the bishop and queen.</li><br>
                                <li><strong>Move 2 — Nf3:</strong> Develops the knight toward the center, attacks the e5 square, and does not block the bishop on f1.</li><br>
                                <li><strong>Move 3 — Bc4:</strong> Develops the bishop to an active diagonal aiming at the center and Black\'s kingside — and clears the path for castling.</li><br>
                                <li><strong>Move 4 — d3:</strong> Supports the center and opens the diagonal for the other bishop — development continues.</li>
                                <li><strong>Move 5 — O-O:</strong> King is safe. Rook moves to f1 and becomes active. Two benefits in one move.</li><br>
                            </ul>
                            <br>
                            <p>Five moves. Every single one serves at least one of the five principles. No time wasted, no piece moved twice, no early queen adventure, king safely tucked away. This is what a good opening looks like.</p>
                        '
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p><strong>Coach\'s tip:</strong> You do not need to memorize specific openings to play good chess. Beginners who memorize five moves of the Sicilian Defence without understanding why each move is played will be lost the moment their opponent deviates. Understanding these five principles means you will always know what to do — regardless of what your opponent plays. Principles over memorization. Always.</p>
                        '
                    ],
                ]
            ]
        );

        /*
        ================================
        LESSON 2 — COURSE SUMMARY
        ================================

        COACHING STRUCTURE:
        1. Opening    — Acknowledge the progression: they now have a plan for every opening
        2. The recap  — All five principles, stated cleanly as a checklist to carry into games
        3. The system — Frame the five principles as one connected idea, not five separate rules
        4. What's next — Honest, specific pointers to where opening study goes from here
        5. Close      — Short, confident send-off
        ================================
        */

        Lesson::updateOrCreate(
            ['slug' => 'opening-principles-summary'],
            [
                'chapter_id' => $chapter3->id,
                'title' => 'Course Summary',
                'order' => 2,
               
                'content_blocks' => [
                    [
                        'type' => 'text',
                        'content' => '
                            <p>When you started this course, the opening was the most confusing part of chess. Sixteen pieces, nobody attacking yet, and no obvious idea of what to do first.</p>
                            <br>
                            <p>That is no longer true. You now have a plan for every opening of every game you will ever play.</p>
                        '
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <h3>The Five Principles</h3>
                            <br>
                            <p>Carry these into every game. Before every move in the opening, ask yourself how many of them your move satisfies.</p>
                            <br>
                            <ul>
                                <li><strong>Control the center.</strong> Fight for e4, d4, e5, and d5 from your very first move. Pieces in the center are more powerful than pieces on the edges.</li><br>
                                <li><strong>Develop your pieces.</strong> Get your knights and bishops off the back rank and into active positions. Develop a new piece every move — knights before bishops, minor pieces before the rooks.</li><br>
                                <li><strong>Avoid moving the same piece twice.</strong> Every time you move a piece that has already moved, you give your opponent a free developing move. Unless you are forced to or you win material, develop a new piece instead.</li><br>
                                <li><strong>Do not bring the queen out too early.</strong> The queen gets chased by cheaper pieces, and every chase costs you time. Bring her out after your minor pieces are developed and your king is safe.</li><br>
                                <li><strong>Castle early.</strong> A king in the center is a target. Get the king to safety within your first ten moves — ideally sooner. Castling also activates the rook, giving you two benefits in one move.</li>
                            </ul>
                        '
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <h3>These Are Not Five Rules — They Are One Idea</h3>
                            <br>
                            <p>The five principles are not independent. They all serve a single goal: reaching the middlegame with your pieces active, your center strong, and your king safe — while your opponent has not.</p>
                            <br>
                            <p>Control the center because that is where power comes from. Develop because that is how you control the center. Avoid moving the same piece twice because every wasted move is a piece that stays undeveloped. Keep the queen back because an early queen gets chased and loses you the development race. Castle because all of that development is meaningless if your king gets attacked before you can use it.</p>
                            <br>
                            <p>One connected idea. Five ways of expressing it.</p>
                        '
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <h3>What Comes Next</h3>
                            <br>
                            <p>You now understand opening principles. Here is where your chess education goes from here:</p>
                            <br>
                            <ul>
                                <li><strong>Specific openings.</strong> There are hundreds of named openings — the Italian Game, the Sicilian Defence, the French Defence, the King\'s Indian. Each one is a specific application of the principles you have already learned. Studying openings becomes much easier once you understand why each move is played.</li><br>
                                <li><strong>Tactics.</strong> Short combinations that win material or deliver checkmate — forks, pins, skewers, discovered attacks. Tactics are the most efficient way to improve quickly, and they show up in every phase of the game.</li><br>
                                <li><strong>Middlegame plans.</strong> Once the opening is over, you need a plan. Learning to identify and execute plans in the middlegame is the next big step after the opening.</li><br>
                                <li><strong>Playing games.</strong> Everything above becomes clearer the more you play. Study the principles, then apply them. Every game — won or lost — teaches you something a lesson cannot.</li>
                            </ul>
                        '
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p>The opening is no longer a mystery. You know what you are doing and why.</p>
                            <br>
                            <p>Now sit down at a board, apply everything you have learned, and see what happens.</p>
                        '
                    ],
                ]
            ]
        );
    }
}