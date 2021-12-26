# Advent of Code 2021

These are solutions for [Advent of Code 2021](https://adventofcode.com/2021).

As opposed to previous years, when I did this in non-native languages, this year I just relaxed and just went with PHP.
Because why not.

All ratings ar relative to my skill, not to be taken as granted. Easy for me can be hard for you, as well as a hard
challange must be an easy one for others.

PHP 8.1 required.

## Retro

After solving all but one day I'm satisfied. Yeah, it agitated me at some point beyond what I'd like (Day 18, I'm
looking at you), but most of the tasks were nice, though I'm not competition material. Looking forward to the next year.

## Day 1

Trivial.

## Day 2

Trivial.

## Day 3

Trivial. Part two required total rewrite, but easy enough.

## Day 4

Had to think a little about the approach, but straightfoward after that.

## Day 5

This was also an easy task. As a bonus - part 2 required only one additional line of code.

## Day 6

Brute force worked well, but then I had to resort to the counts. Strightforward.

## Day 7

This was also an easy task. Both parts.

## Day 8

First part was too easy, but second one was a bit tricky. Once I came up with the strategy of differentiating digits,
rest was easy.

## Day 9

First part was too easy once again. However. Second part was not easy. Not at all. Had to touch up on flood fill
algorithm.

## Day 10

Easy.

## Day 11

Got lost somewhere when resetting the energy level, but quickly recovered. Both parts then were a breeze.

## Day 12

This was not fun. I do not like graphs. But, once implemented Dijkstra (no priority queue - i'm just lazy), it was OK.

## Day 13

This was again an easy task. Folding is not hard. However, you can't fold a paper more than 7 times.

## Day 14

This was the first task I almost went to solutions. First par took some fiddling. Second part required a full rewrite,
and was not very easy to debug.

## Day 15

Oh, I see. It's the hard parts now until the end of the advent. The only problem was that my example obviously did not
work if you travel only right and down. Oh, bugger.

## Day 16

Ahh, thank you gods. This was such a relief to find a protocol parsing task. You can call me a mazochist, if you wish,
but I enjoyed this one from start to finish.

## Day 17

This seemed deceptivly simple until I got to the last part. Decided to brute force the hell out of it and leave the task
be.

## Day 18

The second time I almost gave up. This got me so frustrated that I took a break. Still no peeking to solutions, but
yeah. First part got me banging my head against the table, second part was nothing after that. This might be fifth
implementation from scratch. Bug with tree traversal. Bug with the interpretation of action order. Bug with me.

## Day 19

So, now we're left only with the hard ones for real? Or is it true that I just am not that good of a programmer? Who
hasn't coded rotating toroids in assembly on x286 or z80? Hint - I haven't. However, I was familiar with rotation
matrixes. So this just took a hell of refreshing from memory, one wikipedia article and a bug. Bug with the matrix,
which I found in the morning. But then we're back, baby.

When starting this one, I was almost sure that I'll be unable to pass. Never ever 3D transformations have been a part of
my life. Got lucky I guess.

## Day 20

After last two days this one gave me satisfaction because of how easy it was. And a dissatisfaction because of how easy
it was. And another satisfaction because of that trick in the actual dataset, which was not present in test dataset. Oh,
boy that smile on my face, when I discovered why my code was not working with the real data.

## Day 21

As easy as it should have been, got stuck on recursion and off-by-one errors. Went for math'y approach, when you can
calculate the result instead of playing all possible combinations. TIL that this kind of simple cache is called
memoization.

## Day 22

Algo was easy, implementation was not. Maybe I'm just getting old at last (I'm 42).

1. God some paper and pencils. Used them. Helped a lot.
2. Of course, I could not escape those pesky one-off errors.
3. Spent a lot of time on splitting math. Coordinates for some reason always were mixed up. After some time resorted to
   creating test cases and ahering to them.
4. Even more time was spent searching for non existant bugs. Why, you might ask? Somehow I missed that par 2 sample is
   not the same sample as in part 1. So my result did not match no matter what I do.

## Day 23

Solution is farily trivial in retrospect. That did not help me, as it took 4 hours to code this out even knowing the
theoretical solution behind this.

## Day 24

OK, my mojo was not as strong. Bashed my head against the wall for a while, but then decided to give in. Went over
different solutions, took the cutest one (stack interpretation) and just trasnlated to PHP.

How far did I get on my own? Noticed 14 instruction blocks, extracted 3 changing variables, x and y ar local variables (
y even used twice), eql is just negating, deduced that it all got something to do with base-26, but that was it.

[Original Python](https://old.reddit.com/r/adventofcode/comments/rnejv5/2021_day_24_solutions/hpuu3e0/). BTW, learned a
lot.

## Day 25

Part 1 was trivial. However, you can't get to part 2 without completing all days. Ergm.