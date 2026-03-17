<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Chapter;
use App\Models\Lesson;

class EndgamePrinciplesSeeder extends Seeder
{
    public function run(): void
    {
        $course = Course::updateOrCreate(
            ['slug' => 'endgame-principles'],
            [
                'title' => 'Endgame Principles',
                'description' => '
                    <p>Most games are won and lost in the endgame — and most beginners do not know how to finish them.</p>
                    <br>
                    <p>You have learned the rules, the opening, and the middlegame. Now comes the final phase: fewer pieces, more precision, and the moment where everything you have built either converts into a win or slips away into a draw.</p>
                    <br>
                    <p>In this course, you will learn the most important endgame ideas every player needs — how the king transforms from a piece to protect into your most powerful weapon, how to use the opposition, how to promote a pawn, how to deliver basic checkmates, and how to use a rook in the endgame.</p>
                    <br>
                    <p>These are not advanced techniques. They are the foundations that every complete chess player must have. By the end of this course, you will know how to finish games you have already won.</p>
                ',
            ]
        );

        /*
        ============================================
        CHAPTER 1 — THE KING WAKES UP
        ============================================

        The single most important mindset shift in endgame play.
        Students have spent three courses learning to protect the king.
        Now they must unlearn that instinct — in the endgame, the king
        is a powerful fighting piece that must be centralized and activated.

        LESSON BREAKDOWN:
        Lesson 1 — The Active King  : Why and how the king becomes a fighter in the endgame
        Lesson 2 — The Opposition   : The fundamental king vs king technique
        ============================================
        */

        $chapter1 = Chapter::updateOrCreate(
            ['course_id' => $course->id, 'order' => 1],
            ['title' => 'The King Wakes Up']
        );

        /*
        ============================================
        LESSON 1 — THE ACTIVE KING
        ============================================

        COACHING STRUCTURE:
        1. Hook      — The king spent the whole game hiding; now everything changes
        2. Why now   — Fewer pieces means fewer threats; the king is safer in the center
        3. See it    — Board: king in corner vs king in center; count squares each controls
        4. The rule  — Centralize the king as soon as the endgame begins
        5. See it    — Board: active king supporting a pawn vs passive king losing the pawn
        6. See it    — Board: king march to support a passed pawn
        7. Practice  — Board: centralize the king before making any other move
        8. Coach tip — In the endgame, a king in the center is not in danger — it is in control
        ============================================
        */

        Lesson::updateOrCreate(
            ['slug' => 'the-active-king'],
            [
                'chapter_id' => $chapter1->id,
                'title' => 'The Active King',
                'order' => 1,
                'xp_reward' => 15,
                'content_blocks' => [
                    [
                        'type' => 'text',
                        'content' => '
                            <p>For three courses, you have been told the same thing: protect your king. Tuck it in the corner, castle early, keep it away from the action. The king is precious — not powerful.</p>
                            <br>
                            <p>In the endgame, everything changes.</p>
                            <br>
                            <p>When most of the pieces have been traded off and only a few remain, the king is no longer in danger of being mated by a sudden combination. There are simply not enough enemy pieces left to mount a mating attack. The king is free — and that freedom comes with a responsibility. A king hiding in the corner in the endgame is a wasted piece. A king marching to the center is one of the most powerful forces on the board.</p>
                            <br>
                            <p>The same principle you learned about knights applies to kings: a piece in the center controls more squares than a piece on the edge. A king in the center of the board influences squares in every direction. A king in the corner influences almost nothing.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/king-center-vs-corner',
                            'instructions' => 'Two kings — one on e4 in the center, one on a1 in the corner. Count how many squares each king controls. The central king controls up to 8 squares. The corner king controls only 3. Same piece, completely different power. In the endgame, the king in the center wins. The king in the corner loses.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p>Activating the king is not just about controlling squares. It is about participation. In pawn endgames, the king must escort pawns to promotion, blockade the opponent\'s passed pawns, and fight for key squares. In rook endgames, the king supports its own pawns from behind. A king that reaches the action in time changes the result of the game. A king that stays in the corner watches helplessly.</p>
                            <br>
                            <p>The moment you recognize the endgame has arrived — queens have been traded, only a few pieces remain — your first instinct should be: <strong>bring the king to the center</strong>. Not later. Not after you have made a few pawn moves. Now.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/active-vs-passive-king',
                            'instructions' => 'Two similar pawn endgames, side by side. In one, White centralizes the king immediately and marches it toward the passed pawn. In the other, White leaves the king on g1. Watch the difference in outcome. The active king escorts the pawn to promotion. The passive king arrives too late.',
                        ]
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/king-march',
                            'instructions' => 'The endgame has just begun. Your king is on g1 and there is a passed pawn on the d-file. Before pushing the pawn, march the king toward the center. Find the most direct route for the king to reach the action — and notice how each step it takes increases its influence on the position.',
                        ]
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/centralize-first',
                            'instructions' => 'You have a pawn advantage. There are two moves available — push a pawn, or centralize the king. Which one builds a more decisive advantage? Centralize the king first and see how the position improves before any pawn moves are made.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p><strong>Coach\'s tip:</strong> The single most common endgame mistake at every level below master is leaving the king on the back rank while trying to promote a pawn. The king looks safe there, so players are reluctant to march it forward. But in the endgame, a king in the center is not in danger — it is in control. The sooner you internalize that, the better your endgames will become.</p>
                        '
                    ],
                ]
            ]
        );

        /*
        ============================================
        LESSON 2 — THE OPPOSITION
        ============================================

        COACHING STRUCTURE:
        1. Hook      — Two kings facing each other creates a tension that decides pawn endgames
        2. Definition — The opposition: kings on the same file or rank with one square between them; the side to move loses a tempo
        3. Direct    — Direct opposition: kings facing on the same file with one square between
        4. See it    — Board: direct opposition; the side to move must give way
        5. Why it    — The side NOT to move holds the opposition; forces the other king to step aside
        6. Key use   — Opposition determines whether K+P vs K is a win or a draw
        7. See it    — Board: White has the opposition and the pawn promotes; without opposition, it draws
        8. How to    — How to take the opposition: move to a square where the kings are separated by one square with the opponent to move
        9. Practice  — Board: maneuver to gain the opposition
        10. Coach tip — In king and pawn endgames, whoever controls the opposition controls the game
        ============================================
        */

        Lesson::updateOrCreate(
            ['slug' => 'the-opposition'],
            [
                'chapter_id' => $chapter1->id,
                'title' => 'The Opposition',
                'order' => 2,
                'xp_reward' => 15,
                'content_blocks' => [
                    [
                        'type' => 'text',
                        'content' => '
                            <p>In the endgame, the two kings sometimes face each other with only one square between them. When this happens, neither king can advance directly toward the other — they would be walking into each other\'s control. This creates a moment of tension, and the outcome depends on a single question: whose turn is it to move?</p>
                            <br>
                            <p>This is called the <strong>opposition</strong>.</p>
                            <br>
                            <p>When two kings stand on the same file, rank, or diagonal with exactly one square between them, the player whose turn it is to move is at a disadvantage. They must step aside — their king has to give way — and the other king advances. The player who does <strong>not</strong> have to move is said to "hold the opposition," and holding the opposition is a significant advantage in king and pawn endgames.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/direct-opposition',
                            'instructions' => 'The kings are facing each other directly on the same file with one square between them — this is called direct opposition. It is Black\'s turn to move. Black\'s king must step aside to one side or the other. Watch how White\'s king follows and advances. The king that holds the opposition — the one that does NOT have to move — gets to push forward.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p>Why does this matter so much? Because in king and pawn endgames, a pawn can only promote if its king can clear the path. And the only thing stopping the king from clearing that path is the enemy king. The opposition is the tool that determines which king wins the battle — the attacking king that wants to escort the pawn, or the defending king that wants to blockade it.</p>
                            <br>
                            <p>When the attacking king holds the opposition, it can force the defending king out of the way and escort the pawn to promotion. When the defending king holds the opposition, it can hold its ground and prevent the pawn from getting through. The same position, one square of difference in king placement, completely different result.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/opposition-wins-vs-draws',
                            'instructions' => 'Two almost identical positions — same pawn, same kings, but one square of difference in where White\'s king stands. In the first position, White holds the opposition and the pawn promotes. In the second, Black holds the opposition and the game draws. Watch both outcomes and see exactly how that one square changes everything.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p>To gain the opposition, you need to maneuver your king to a square where the kings will be separated by exactly one square — and it will be your opponent\'s turn to move when you get there. This often requires counting moves carefully and sometimes taking an indirect route.</p>
                            <br>
                            <p>A simple way to think about it: if the kings are on the same file or rank with an odd number of squares between them, the player to move has the opposition. If there is an even number of squares between them, the player to move does not have the opposition — they can gain it by moving toward the other king.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/gain-opposition',
                            'instructions' => 'White needs to gain the opposition before the defending king can block the pawn. Find the king maneuver that positions White\'s king with the opposition — one square separating the kings, with Black to move. Take the indirect route if needed.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p><strong>Coach\'s tip:</strong> The opposition is not a complicated idea, but it takes time to feel natural. The best way to develop this skill is to play out king and pawn endgames from both sides — as the attacker trying to promote, and as the defender trying to hold. You will quickly develop an instinct for when you have the opposition and when you need to maneuver to gain it.</p>
                        '
                    ],
                ]
            ]
        );

        /*
        ============================================
        CHAPTER 2 — PAWN ENDGAMES
        ============================================

        The most fundamental endgame category. Built directly on king
        activity and the opposition. K+P vs K is the single most important
        endgame every player must know — it is the endgame every promoted
        pawn leads to, and it is decided entirely by what was taught in Chapter 1.

        LESSON BREAKDOWN:
        Lesson 1 — King and Pawn vs King  : The essential endgame; win vs draw determined by king position and opposition
        Lesson 2 — Passed Pawns in the Endgame : How passed pawns win in the endgame; outside passed pawn; connected passed pawns
        ============================================
        */

        $chapter2 = Chapter::updateOrCreate(
            ['course_id' => $course->id, 'order' => 2],
            ['title' => 'Pawn Endgames']
        );

        /*
        ============================================
        LESSON 1 — KING AND PAWN VS KING
        ============================================

        COACHING STRUCTURE:
        1. Hook      — The most common endgame in chess; knowing it changes how you play the whole game
        2. The rule  — King in front of the pawn: the key principle
        3. See it    — Board: king in front of pawn, wins with opposition
        4. The draw  — King beside the pawn or behind it is usually a draw with correct defense
        5. See it    — Board: king beside pawn, defender holds with opposition
        6. Exception — Rook pawn (a or h file) is always a draw without the king trapped in the corner
        7. See it    — Board: rook pawn exception demonstrated
        8. The rule  — The defending king runs to the queening square; if it gets there in time, it draws
        9. Practice  — Board: determine whether this K+P vs K position is a win or a draw
        10. Coach tip — Before trading into a pawn endgame, always calculate whether it is won or drawn
        ============================================
        */

        Lesson::updateOrCreate(
            ['slug' => 'king-and-pawn-vs-king'],
            [
                'chapter_id' => $chapter2->id,
                'title' => 'King and Pawn vs King',
                'order' => 1,
                'xp_reward' => 15,
                'content_blocks' => [
                    [
                        'type' => 'text',
                        'content' => '
                            <p>King and pawn vs king is the most fundamental endgame in chess. It is the endgame that every single pawn promotion leads to — the moment where one player has a pawn that can become a queen, and the other player has only their king to stop it.</p>
                            <br>
                            <p>Understanding whether this endgame is a win or a draw changes how you think about the whole game. Players who know this endgame are confident trading into pawn endings. Players who do not know it avoid them, even when a won pawn ending is sitting right in front of them.</p>
                            <br>
                            <p>The most important principle is this: <strong>the attacking king must get in front of the pawn.</strong></p>
                            <br>
                            <p>A king that leads the pawn — standing one or two squares ahead of it, toward the promotion square — can use the opposition to push the defending king out of the way. A king that sits beside the pawn or trails behind it cannot do this effectively. The pawn stalls, the defender holds the opposition, and the game draws.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/king-front-of-pawn-wins',
                            'instructions' => 'White\'s king is in front of the pawn — the winning configuration. Watch how White uses the opposition to force Black\'s king out of the way, step by step, until the pawn can advance safely to the last rank. Every move the White king makes either gains the opposition or advances the pawn when the path is clear.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p>Now let us see the draw. If the defending king can reach the square directly in front of the pawn and hold the opposition, the pawn cannot advance. The game ends in a draw — stalemate or repetition — no matter how many extra moves the attacking side tries.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/king-beside-pawn-draws',
                            'instructions' => 'White\'s king is beside the pawn rather than in front of it. Black\'s defending king runs directly to the queening square and holds the opposition. Watch how Black holds the draw by staying in front of the pawn every time White tries to advance. This is correct defensive technique.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p>There is one important exception: the <strong>rook pawn</strong>.</p>
                            <br>
                            <p>A pawn on the a-file or h-file — the edge of the board — is almost always a draw, regardless of king position. Here is why: when the attacking king tries to push the defending king away from the queening square (a8 or h8), the defending king gets pushed into the corner. But the corner is not a trap — it is a refuge. The pawn cannot promote because the attacking king, not the pawn, would occupy the promotion square, resulting in stalemate. Or the defending king simply shuffles back and forth between the corner and the edge, and the pawn can never advance.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/rook-pawn-draw',
                            'instructions' => 'White has an h-pawn and what looks like a winning king position. But Black\'s king runs to h8 — the corner — and draws by stalemate or endless shuffling. Watch how the rook pawn exception works, and why the corner becomes a safe haven for the defender.',
                        ]
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/win-or-draw',
                            'instructions' => 'Look at this K+P vs K position and decide before moving: is this a win for White, or a draw with correct play from Black? Think about where the kings are, who has the opposition, and whether the pawn is on the edge of the board. Then play it out and see if your assessment was correct.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p><strong>Coach\'s tip:</strong> Before you trade pieces into a pawn endgame — especially late in the game when you think you are winning — always check whether the resulting K+P vs K position is actually won. Many players have traded a winning middlegame position into a drawing endgame because they did not know this rule. King in front of the pawn, not a rook pawn: that is the winning formula.</p>
                        '
                    ],
                ]
            ]
        );

        /*
        ============================================
        LESSON 2 — PASSED PAWNS IN THE ENDGAME
        ============================================

        COACHING STRUCTURE:
        1. Hook      — Passed pawns in the middlegame tie pieces down; in the endgame they win games
        2. Recall    — Quick reference to the middlegame lesson; now the context is different
        3. Rule      — Passed pawns must be pushed; in the endgame there is no time to wait
        4. See it    — Board: passed pawn with king escort vs lone defending king
        5. Outside   — The outside passed pawn: a passed pawn on the wing forces the enemy king away while your king invades
        6. See it    — Board: outside passed pawn decoy winning the game
        7. Connected — Two connected passed pawns beat a rook; the concept simply introduced
        8. See it    — Board: connected passers advancing unstoppably
        9. Practice  — Board: use the passed pawn + king to convert the win
        10. Coach tip — A passed pawn in the endgame is almost always a winning advantage; the question is how to convert it
        ============================================
        */

        Lesson::updateOrCreate(
            ['slug' => 'passed-pawns-endgame'],
            [
                'chapter_id' => $chapter2->id,
                'title' => 'Passed Pawns in the Endgame',
                'order' => 2,
                'xp_reward' => 15,
                'content_blocks' => [
                    [
                        'type' => 'text',
                        'content' => '
                            <p>In the middlegame, a passed pawn is a long-term weapon — it ties down pieces, restricts the opponent, and creates a constant threat. In the endgame, it becomes something more direct: a pawn that is close to becoming a queen, with very little left on the board to stop it.</p>
                            <br>
                            <p>The rule you learned still applies — <strong>passed pawns must be pushed</strong> — but in the endgame the urgency is greater. There are no pieces left to blockade it indefinitely. The only defender is the enemy king, and the king can only be in one place at a time. Push the pawn, bring your king to escort it, and the promotion is often unstoppable.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/passed-pawn-escort',
                            'instructions' => 'White has a passed pawn on d5 and a king to escort it. The defending king is far away. Watch how White pushes the pawn and brings the king forward together — the king clears the path and the pawn promotes. This is the cleanest version of converting a passed pawn in the endgame.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p>One of the most powerful endgame concepts is the <strong>outside passed pawn</strong>.</p>
                            <br>
                            <p>When you have a passed pawn on one side of the board and pawns on the other side, you can use the passed pawn as a decoy. Push it toward promotion on the far side. The enemy king must chase it — it has no choice; if it ignores the pawn it promotes. While the defending king sprints to stop the passed pawn, your own king invades on the other side of the board and captures the undefended pawns. By the time the defending king returns, it is too late.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/outside-passed-pawn',
                            'instructions' => 'White has an outside passed pawn on the a-file and a pawn majority on the kingside. Push the a-pawn toward promotion. Watch how Black\'s king is forced to chase it — and while it does, White\'s king walks across the board and captures the kingside pawns. The a-pawn does not even need to promote; it just needs to be threatening enough to pull the enemy king away.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p>When you have <strong>two connected passed pawns</strong> — two pawns on adjacent files, both passed, each protecting the other — they become almost impossible to stop. Together they advance as a unit. If the enemy king tries to capture one, the other advances and promotes. If it tries to block both, it cannot — it can only stand in front of one at a time. Two connected passers in the endgame can even defeat a rook if they are advanced enough.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/connected-passers',
                            'instructions' => 'White has two connected passed pawns on e5 and f5. Watch them advance together — when Black\'s king tries to stop one, the other slips through. Neither pawn can be captured without allowing the other to promote. This is why connected passed pawns are one of the most powerful advantages in the endgame.',
                        ]
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/convert-passed-pawn',
                            'instructions' => 'White has a passed pawn and a king advantage. Convert the win — bring your king to escort the pawn, push it toward promotion, and use the opposition if the defending king tries to block. There is more than one path to promotion; find the most efficient one.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p><strong>Coach\'s tip:</strong> In the endgame, a passed pawn is almost always a decisive advantage — the question is not whether it wins, but how to convert it efficiently. The two mistakes to avoid: pushing the pawn without the king nearby to support it, and waiting too long to push it while the defending king gets into position. Push early, push with the king nearby, and the pawn will do the rest.</p>
                        '
                    ],
                ]
            ]
        );

        /*
        ============================================
        CHAPTER 3 — CHECKMATING & ROOK ENDGAMES
        ============================================

        The practical payoff of the course. Students learn how to actually
        finish games — delivering checkmate with basic material, and handling
        the most common endgame type in practice (rook endgames).

        LESSON BREAKDOWN:
        Lesson 1 — K+Q vs K       : The simplest checkmate to deliver; lawnmower method
        Lesson 2 — K+R vs K       : Slightly harder; box method
        Lesson 3 — Basic Rook Endgames : Rook + pawn vs rook; active rook; rook behind the passed pawn
        ============================================
        */

        $chapter3 = Chapter::updateOrCreate(
            ['course_id' => $course->id, 'order' => 3],
            ['title' => 'Checkmating & Rook Endgames']
        );

        /*
        ============================================
        LESSON 1 — KING AND QUEEN VS KING
        ============================================

        COACHING STRUCTURE:
        1. Hook      — You promoted your pawn to a queen; now you need to know how to finish it
        2. The goal  — Drive the enemy king to the edge, then checkmate it
        3. Method    — The lawnmower: queen cuts off rows one at a time, shrinking the king's space
        4. See it    — Board: queen cutting off ranks step by step
        5. Key danger — Stalemate; the one trap that turns a win into a draw
        6. See it    — Board: stalemate example; how to avoid it
        7. Finish    — Deliver checkmate once the king is on the edge
        8. See it    — Board: checkmate delivered with K+Q
        9. Practice  — Board: full K+Q vs K from a starting position; deliver checkmate
        10. Coach tip — Give the king space, not stalemate; check before every move
        ============================================
        */

        Lesson::updateOrCreate(
            ['slug' => 'kq-vs-k'],
            [
                'chapter_id' => $chapter3->id,
                'title' => 'King and Queen vs King',
                'order' => 1,
                'xp_reward' => 20,
                'content_blocks' => [
                    [
                        'type' => 'text',
                        'content' => '
                            <p>You have a queen and a king against a lone king. This is won — the queen is far too powerful for the defending king to survive forever. But many beginners reach this position and stall, pushing the enemy king around the board without ever delivering checkmate, or worse — accidentally stalemating it and throwing away the win.</p>
                            <br>
                            <p>The key is a method. King and queen vs king is not hard if you follow a clear plan.</p>
                            <br>
                            <p><strong>The goal</strong>: drive the enemy king to the edge of the board — any edge — and then deliver checkmate there. A king in the center cannot be checkmated; there are too many squares to escape to. A king on the edge has far fewer options.</p>
                            <br>
                            <p><strong>The method</strong>: use the queen to cut off rows. Move the queen to a rank or file that limits how far the enemy king can move. Then cut off another row. The king\'s available space shrinks with every move until it is on the edge of the board and your own king has joined the attack.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/queen-cuts-off-rows',
                            'instructions' => 'Watch the queen cut off ranks one at a time — first limiting the king to the bottom half of the board, then to the bottom third, then pushing it to the last rank. The queen does not check the king on every move; it systematically shrinks the available space. Follow the logic of each queen move.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p>There is one danger you must always watch for: <strong>stalemate</strong>.</p>
                            <br>
                            <p>Remember from the Check and Checkmate lesson — stalemate is when the enemy king has no legal moves but is not in check. The game is immediately a draw. With a queen, it is surprisingly easy to stalemate accidentally. If the enemy king is in a corner with no moves, and you give check, stalemate can happen in one careless move.</p>
                            <br>
                            <p>Before every queen move, ask: after I make this move, does the enemy king have at least one legal square? If the answer is no, and the king is not in check, you have stalemated. Always leave the king an escape square — then take it away with checkmate, not stalemate.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/stalemate-trap',
                            'instructions' => 'White is one move from checkmate — but there is a stalemate trap. If White plays carelessly, the next move is stalemate and the win is gone. Find the move that avoids stalemate while keeping the king trapped. Check: does the king have at least one legal move after your queen move?',
                        ]
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/kq-vs-k-full',
                            'instructions' => 'The enemy king is in the center of the board. Start from here and deliver checkmate — use the queen to cut off rows, drive the king to the edge, bring your own king close, and finish with checkmate. Watch for stalemate at every step. Try to do it in under 20 moves.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p><strong>Coach\'s tip:</strong> The most reliable habit for avoiding stalemate is to check, after every single queen move, whether the enemy king has at least one legal square available. If it does not — and it is not in check — you have blundered. Slow down. Give the king a square, then take it away with checkmate. One extra move to avoid stalemate is always worth it.</p>
                        '
                    ],
                ]
            ]
        );

        /*
        ============================================
        LESSON 2 — KING AND ROOK VS KING
        ============================================

        COACHING STRUCTURE:
        1. Hook      — Harder than K+Q but still completely won with the right method
        2. The goal  — Same as K+Q: drive the king to the edge; rook alone cannot checkmate in the center
        3. Method    — The box: use the rook to create a box around the enemy king and shrink it
        4. See it    — Board: rook creating and shrinking the box
        5. King helps — The attacking king must participate; the rook cannot do it alone
        6. See it    — Board: king and rook working together to shrink the box to the edge
        7. Checkmate — How checkmate is delivered: king on the edge, rook gives check, own king covers escape squares
        8. See it    — Board: final checkmate position demonstrated
        9. Key danger — Stalemate again; slightly less likely but still possible
        10. Practice  — Board: deliver K+R vs K checkmate from a mid-board position
        11. Coach tip — Be patient; K+R vs K takes more moves than K+Q but the method always works
        ============================================
        */

        Lesson::updateOrCreate(
            ['slug' => 'kr-vs-k'],
            [
                'chapter_id' => $chapter3->id,
                'title' => 'King and Rook vs King',
                'order' => 2,
                'xp_reward' => 20,
                'content_blocks' => [
                    [
                        'type' => 'text',
                        'content' => '
                            <p>King and rook vs king is harder than king and queen vs king — the rook is less powerful, so it takes more moves and more precision. But it is still completely won. There is a method, and the method always works.</p>
                            <br>
                            <p>The goal is the same: drive the enemy king to the edge of the board, then deliver checkmate there. A rook cannot checkmate a king in the center — there are too many escapes. On the edge, the board itself helps trap the king.</p>
                            <br>
                            <p>The method is called <strong>the box</strong>. The rook creates an imaginary box around the enemy king by placing itself on a rank or file that the king cannot cross. Every time the king approaches the edge of the box, the rook shrinks the box — moving to a closer rank or file, tightening the space available. Slowly and systematically, the king is pushed to the edge.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/rook-box-method',
                            'instructions' => 'The rook creates a box — the enemy king is confined to the top half of the board by a rook on the 4th rank. When the king approaches the rook\'s rank, the rook moves to the 3rd rank, shrinking the box. Watch this shrinking process and see how the king is systematically pushed toward the edge.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p>Here is an important difference from the queen endgame: the rook cannot deliver checkmate alone. The attacking king must actively participate. Once the rook has driven the enemy king to the edge, the attacking king needs to approach and take away the remaining escape squares, while the rook delivers the final check.</p>
                            <br>
                            <p>The final checkmate position looks like this: enemy king on the edge, attacking king on an adjacent diagonal square cutting off escapes, rook giving check along the edge. Together they leave no legal moves.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/king-rook-coordination',
                            'instructions' => 'The enemy king has been pushed to the 8th rank. Now White\'s own king needs to approach to help deliver checkmate. Bring the White king to the right square while the rook holds the king on the edge. Then deliver checkmate — rook gives check along the 8th rank, king covers the escape squares.',
                        ]
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/kr-vs-k-full',
                            'instructions' => 'The enemy king is in the center of the board. Deliver checkmate using the box method — create a box with the rook, shrink it, bring your king forward, and finish with checkmate on the edge. This will take more moves than the queen endgame. Be patient and systematic.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p><strong>Coach\'s tip:</strong> K+R vs K takes longer than K+Q vs K — typically 15 to 20 moves even with good technique. Do not rush. The box method is reliable; trust it. If the enemy king ever escapes back toward the center, just rebuild the box and start shrinking again. The one thing to avoid is giving the rook away by placing it where the enemy king can capture it. Keep the rook at a safe distance while maintaining the box.</p>
                        '
                    ],
                ]
            ]
        );

        /*
        ============================================
        LESSON 3 — BASIC ROOK ENDGAMES
        ============================================

        COACHING STRUCTURE:
        1. Hook      — Rook endgames are the most common endgame in chess; two key principles decide them
        2. Principle — Active rook vs passive rook: the rook behind a passed pawn is the most active rook
        3. See it    — Board: rook behind passed pawn vs rook in front of passed pawn
        4. Principle — Cut off the enemy king: use the rook to restrict the defending king to one side of the board
        5. See it    — Board: rook cutting off the king on the 4th rank, enabling king and pawn to advance
        6. The draw  — Passive rook defense: the Lucena and Philidor concepts named and described simply without deep theory
        7. See it    — Board: defending rook checking from the side (the concept, not the technique)
        8. Practice  — Board: place the rook behind the passed pawn and use it to escort the pawn to promotion
        9. Coach tip — Keep the rook active; a rook doing nothing in the endgame is a half-piece
        ============================================
        */

        Lesson::updateOrCreate(
            ['slug' => 'basic-rook-endgames'],
            [
                'chapter_id' => $chapter3->id,
                'title' => 'Basic Rook Endgames',
                'order' => 3,
                'xp_reward' => 20,
                'content_blocks' => [
                    [
                        'type' => 'text',
                        'content' => '
                            <p>Rook endgames are the most common endgame type in chess. More games end in rook endgames than any other type — which means understanding a few key ideas here will improve more of your actual games than almost anything else in this course.</p>
                            <br>
                            <p>The good news: at this level, two principles cover the most important ground.</p>
                            <br>
                            <p><strong>Principle 1: Put your rook behind the passed pawn.</strong></p>
                            <br>
                            <p>A rook placed directly behind a passed pawn — on the same file, pointing toward the promotion square — is the most active rook position in the endgame. As the pawn advances, the rook\'s range increases, not decreases. The rook supports every step the pawn takes from the rear, and it can never be blocked by the pawn it is supporting.</p>
                            <br>
                            <p>A rook placed in front of the pawn, by contrast, is blocking its own pawn. As the pawn advances, the rook must keep moving out of its way. A rook placed beside the pawn has limited support. Behind the pawn is always the best square.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/rook-behind-passed-pawn',
                            'instructions' => 'Compare two positions: in one, White\'s rook is behind the passed pawn on d1, supporting it as it advances. In the other, White\'s rook is in front of the pawn on d6, blocking it. Watch how the rook behind the pawn stays maximally active the entire time, while the rook in front has to keep retreating. Behind the pawn is almost always correct.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p><strong>Principle 2: Cut off the enemy king.</strong></p>
                            <br>
                            <p>One of the most effective things a rook can do in the endgame is restrict where the enemy king can go. By placing the rook on a rank or file between the enemy king and the passed pawn, you cut the king off — it cannot cross that line to attack the pawn or interfere with your king\'s advance.</p>
                            <br>
                            <p>A king that is cut off on the far side of the board is irrelevant. Your king and pawn advance freely on the other side while the enemy king watches from behind a barrier it cannot cross.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/rook-cuts-off-king',
                            'instructions' => 'White places the rook on the 4th rank, cutting the Black king off from the queenside. Black\'s king cannot cross that rank — every time it tries, the rook is there. With the enemy king neutralized, White\'s king and pawn advance freely. Watch how the cut-off dramatically simplifies the win.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p>When you are the defending side in a rook endgame, the key is to keep your rook <strong>active</strong> — checking the enemy king from the side, harassing it, and making it difficult for the attacker to coordinate king and pawn. Passive defense, where the rook just blocks the pawn from the front, is usually losing. Active defense, where the rook keeps giving check and forcing the king to move, can often save the game.</p>
                            <br>
                            <p>The specific techniques for drawing rook endgames — the Lucena position and the Philidor position — are named methods that go deeper than this course. But the underlying idea is simple: if you are defending, keep the rook active. Do not let it become passive. An active rook checking from the side is far harder to deal with than a rook sitting still.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/active-defensive-rook',
                            'instructions' => 'Black is defending a rook endgame with a pawn down. Instead of sitting passively, the Black rook gives check from the side — forcing the White king to keep moving and preventing it from escorting the pawn. Watch how active defense makes life much harder for the attacker than passive defense would.',
                        ]
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/rook-endgame-practice',
                            'instructions' => 'White has a rook and a passed pawn. Apply both principles: place the rook behind the passed pawn, use it to cut off the enemy king, and escort the pawn toward promotion. Bring the king forward to support when needed. Convert the win.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p><strong>Coach\'s tip:</strong> In any rook endgame, the first question to ask is: where should my rook be? Almost always the answer is behind the passed pawn or cutting off the enemy king — the two most active squares available to the rook. A rook that is doing neither of those things is a passive rook, and a passive rook in the endgame is a rook that is losing the game for you. Keep it active.</p>
                        '
                    ],
                ]
            ]
        );

        /*
        ============================================
        CHAPTER 4 — PUTTING IT TOGETHER
        ============================================

        The closing chapter of both the course and the full four-course series.
        One model endgame showing the concepts in sequence, then a summary
        that closes not just this course but the entire journey from Chess Basics
        through to Endgame Principles.

        LESSON BREAKDOWN:
        Lesson 1 — A Model Endgame     : King activity, opposition, passed pawn, and checkmate in one game
        Lesson 2 — Course & Series Summary : Full recap + what chess mastery looks like from here
        ============================================
        */

        $chapter4 = Chapter::updateOrCreate(
            ['course_id' => $course->id, 'order' => 4],
            ['title' => 'Putting It Together']
        );

        /*
        ============================================
        LESSON 1 — A MODEL ENDGAME
        ============================================

        COACHING STRUCTURE:
        1. Hook      — All concepts in one game: king activation, opposition, passed pawn, checkmate
        2. Phase 1   — Queens trade off; endgame begins; king immediately centralizes
        3. Phase 2   — Opposition contested; White gains it and the pawn advances
        4. Phase 3   — Outside passed pawn diverts the enemy king
        5. Phase 4   — Pawn promotes; K+Q vs K; checkmate delivered avoiding stalemate
        6. Reflection — Every endgame idea from the course appeared naturally
        7. Practice  — Board: play from the critical moment; make the winning king move
        8. Coach tip  — Endgames reward patience and precision; one good plan, followed to the end
        ============================================
        */

        Lesson::updateOrCreate(
            ['slug' => 'a-model-endgame'],
            [
                'chapter_id' => $chapter4->id,
                'title' => 'A Model Endgame',
                'order' => 1,
                'xp_reward' => 25,
                'content_blocks' => [
                    [
                        'type' => 'text',
                        'content' => '
                            <p>Every concept in this course — king activation, the opposition, passed pawns, basic checkmates — appears not as a standalone exercise but as a natural part of how good endgame play actually unfolds. This game puts them all together.</p>
                            <br>
                            <p>Watch for each idea as it emerges: the moment the queens come off and the king immediately begins its march, the opposition being contested and won, the passed pawn being pushed with the king in front, and finally the checkmate delivered cleanly without stalemate.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/model-endgame-annotated',
                            'instructions' => 'Follow this endgame move by move and read each annotation. You will see the king activated the moment the endgame begins, the opposition contested and won to escort the passed pawn, an outside passed pawn used as a decoy to win the remaining pawns, and a clean K+Q vs K checkmate delivered at the end — avoiding the stalemate trap. Try to predict each move before reading the annotation.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p>None of these ideas required calculation beyond what a patient player can see. The king march was not a combination — it was a habit, the instinct to centralize the moment the endgame began. The opposition was not a memorized line — it was an understanding of how kings interact. The passed pawn conversion was not a prepared sequence — it was the natural result of a clear plan, followed through to the end.</p>
                            <br>
                            <p>That is what endgame mastery looks like at this level: not calculation, but clarity. Knowing what to do and doing it without hesitation.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/model-endgame-practice',
                            'instructions' => 'The position is set to the critical moment from the model game — the point where the winning idea must be found. White needs to make the key king move that gains the opposition and unlocks the passed pawn advance. Find it — and then play out the endgame to checkmate.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p><strong>Coach\'s tip:</strong> Endgames reward a quality that the opening and middlegame do not always require: patience. The attacker who finds the right plan and executes it calmly, without hurrying, without panicking, without taking unnecessary risks — that player wins endgames consistently. One good plan, followed all the way to the end.</p>
                        '
                    ],
                ]
            ]
        );

        /*
        ============================================
        LESSON 2 — COURSE & SERIES SUMMARY
        ============================================

        This is the final lesson of the final course in the series.
        It closes Endgame Principles and the full four-course journey.
        The tone is reflective and forward-looking — acknowledging how
        far the student has come while making clear that this is not
        an ending, it is a foundation.
        ============================================
        */

        Lesson::updateOrCreate(
            ['slug' => 'endgame-summary'],
            [
                'chapter_id' => $chapter4->id,
                'title' => 'Course & Series Summary',
                'order' => 2,
                'xp_reward' => 25,
                'content_blocks' => [
                    [
                        'type' => 'text',
                        'content' => '
                            <p>You started this series not knowing what the pieces did.</p>
                            <br>
                            <p>You now understand how every piece moves, how to set up the board, every special rule in chess, the principles that govern the opening, how to handle the middlegame — tactics, pawn structure, attack and defense — and how to convert advantages in the endgame into wins.</p>
                            <br>
                            <p>That is a complete chess education. Let us close each chapter of it.</p>
                        '
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <h3>This Course — Endgame Principles</h3>
                            <br>
                            <ul>
                                <li><strong>The active king.</strong> In the endgame, the king is a fighter. Centralize it the moment the endgame begins. A king in the center controls more squares, supports pawns, and participates in the win. A king in the corner watches and loses.</li>
                                <li><strong>The opposition.</strong> When two kings face each other with one square between them, the player to move must give way. The player who holds the opposition — who does not have to move — can push forward. In K+P vs K, the opposition decides whether the position is won or drawn.</li>
                                <li><strong>King and pawn vs king.</strong> King in front of the pawn wins. King beside or behind the pawn usually draws. The rook pawn always draws. The defending king runs to the queening square — if it arrives in time, it holds.</li>
                                <li><strong>Passed pawns in the endgame.</strong> Push them. Escort them with the king. Use an outside passed pawn as a decoy to pull the enemy king away. Two connected passed pawns advance as an unstoppable unit.</li>
                                <li><strong>K+Q vs K.</strong> Drive the king to the edge using the queen to cut off rows. Watch for stalemate at every step. Deliver checkmate once the king is on the edge and your own king is close.</li>
                                <li><strong>K+R vs K.</strong> Use the box method — the rook creates a shrinking box around the enemy king. The attacking king must participate. Deliver checkmate on the edge with king and rook coordinating together.</li>
                                <li><strong>Basic rook endgames.</strong> Rook behind the passed pawn. Cut off the enemy king. Keep the rook active — a passive rook loses endgames. If defending, check from the side rather than blocking from the front.</li>
                            </ul>
                        '
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <h3>The Full Series — What You Now Know</h3>
                            <br>
                            <p><strong>Chess Basics</strong> gave you the language of chess — the pieces, the board, the rules. You can play a legal game anywhere in the world.</p>
                            <br>
                            <p><strong>Opening Principles</strong> gave you a plan for the first phase of every game — control the center, develop your pieces, avoid moving the same piece twice, keep the queen back, castle early. You no longer enter the middlegame by accident.</p>
                            <br>
                            <p><strong>Middlegame Principles</strong> gave you the tools to fight — active pieces, pawn structure, four tactical weapons, how to build and survive attacks. You understand what good chess looks like in the middle of the battle.</p>
                            <br>
                            <p><strong>Endgame Principles</strong> gave you the ability to finish games — convert advantages, deliver checkmates, handle the most common endgame scenarios. You no longer win the game and then draw it by accident.</p>
                            <br>
                            <p>Together, these four courses give you the complete arc of a chess game, from the first move to the last.</p>
                        '
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <h3>What Chess Mastery Looks Like From Here</h3>
                            <br>
                            <p>You have the foundation. Everything from this point forward is built on top of it. Here is where serious chess development goes next:</p>
                            <ul>
                                <li><strong>Tactics puzzles — daily.</strong> The fastest way to improve at chess is to solve tactical puzzles every day. Forks, pins, skewers, discovered attacks — and patterns you have not learned yet: back rank mates, zwischenzugs, deflections, decoys. Every pattern you learn is a weapon you carry into every game. Ten puzzles a day will make you a noticeably stronger player within months.</li>
                                <li><strong>Specific openings.</strong> You understand opening principles. Now you are ready to study specific openings — the Italian Game, the Ruy Lopez, the Sicilian Defence, the French Defence — understanding not just the first five moves but the middlegame plans each opening leads to. Choose one or two openings for White and one as Black, and study them properly.</li>
                                <li><strong>Endgame technique.</strong> This course covered the surface. The deeper layer — Lucena, Philidor, bishop and wrong-colored rook pawn, knight and bishop checkmate — is where endgame mastery lives. When you are ready, study rook endgames specifically; they are the most common and the most instructive.</li>
                                <li><strong>Annotated games.</strong> Study games played by strong players — not to memorize moves, but to understand the plans behind them. Every move a strong player makes has a reason. Learning to identify those reasons is how you develop your own chess thinking.</li>
                                <li><strong>Play. Analyze. Repeat.</strong> Every game you play is data. After each game, go back and find your biggest mistake — not the last mistake, but the one that changed the game. Fix that pattern. Play again. This cycle, applied consistently, is how chess players improve at every level from beginner to grandmaster.</li>
                            </ul>
                        '
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p>Chess has been played for over 1,500 years. Grandmasters who have studied it their entire lives still find new ideas in it. You have taken your first real steps into that world.</p>
                            <br>
                            <p>You know the rules. You know the principles. You know how games are won and lost.</p>
                            <br>
                            <p>The rest is practice, patience, and the love of the game.</p>
                            <br>
                            <p>Go play.</p>
                        '
                    ],
                ]
            ]
        );
    }
}