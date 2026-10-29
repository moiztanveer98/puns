# -*- coding: utf-8 -*-
ARTICLES = {}

ARTICLES["puns-for-cards"] = {
    "title": "30+ Puns for Cards for Every Occasion",
    "category_id": 33,
    "intro": "Puns for cards turn a quick birthday, thank-you or get-well message into something the recipient will actually remember. These lines cover the occasions people need card messages for most, from birthdays to new jobs.",
    "sections": [
        ("Birthday Card Puns", "These work for birthday cards of any age, no candles required.", [
            "Age is just a number, and yours is a pretty impressive one.", "Hope your birthday is sew much fun.",
            "You're not getting older, you're leveling up.", "Lettuce celebrate your birthday in style.",
            "You're grape, and I'm so glad it's your birthday.", "Hope your day is un-be-leaf-ably good.",
            "Another year, still a-maize-ing.", "Birthdays are berry special, and so are you.",
        ]),
        ("Thank You Card Puns", "These make a thank-you note feel a little warmer.", [
            "Thanks a bunch, you're grape.", "I'm so grateful, I can't espresso it enough.",
            "You're the cherry on top of my day.", "Thanks a latte for everything you do.",
            "I donut know what I'd do without you.", "You're the zest, thanks for everything.",
            "Thanks for being so egg-cellent.", "I owe you a debt of grate-itude.",
        ]),
        ("Get Well Card Puns", "A little humor can go a long way when someone's under the weather.", [
            "Hope you're feeling less crummy soon.", "Get well soon, you're too a-peel-ing to be sick.",
            "Sending you soup-er healing vibes.", "Hope you bounce back quicker than a rubber band.",
            "Feel better soon, we miss your sparkle.", "Rest up, you'll be back to your punny self soon.",
            "Get well soon, the world needs your puns.",
        ]),
        ("New Job and Congratulations Card Puns", "These fit a new job, promotion, or general life win.", [
            "You're really going places, congrats on the new job.", "Hope this new chapter is grape.",
            "Congrats, you really nailed it.", "You worked hard and it really paid off, literally.",
            "Here's to new beginnings and bigger paychecks.", "You've earned this, now go celebrate.",
            "Wishing you continued success, you're un-stoppable.",
        ]),
    ],
    "how_to_use": "Puns for cards work best when paired with a genuine message; use the pun as an opener or closer and let the sincere line carry the main sentiment, especially for get-well and thank-you cards.",
    "faq": [
        ("What's a good pun for a birthday card?", "Food-based puns like 'grape' and 'a-maize-ing' are popular for birthday cards because they're easy to understand and pair naturally with the word 'celebrate.'"),
        ("Are puns appropriate for sympathy or get-well cards?", "Light, gentle puns can work for get-well cards, but it's best to keep sympathy cards sincere and skip the wordplay entirely."),
        ("How do I write a punny thank-you note?", "Open or close with a short food or coffee-related pun, then follow with a specific, genuine reason you're thankful."),
    ],
    "related": [("/puns-for-instagram-captions/", "Puns for Instagram Captions"), ("/graduation-puns/", "Graduation Puns"), ("/wedding-puns/", "Wedding Puns")],
}

ARTICLES["texting-puns"] = {
    "title": "20+ Texting Puns for Group Chats and DMs",
    "category_id": 33,
    "intro": "Texting puns are built for the small screen, short on words and big on laughs. These lines fit group chats, DMs, and the everyday back-and-forth that makes texting fun.",
    "sections": [
        ("Group Chat Puns", "These work great as a group chat name or a message that gets the thread going.", [
            "This group chat really grinds my coffee beans.", "I'm texting this fast because I'm roll-ing with it.",
            "This chat has zero chill, and I love it.", "Let's taco 'bout our plans for tonight.",
            "I'm on a roll today, pun intended.", "This conversation is a-maize-ing, don't stop.",
            "I'm all ears, or in this case, all thumbs.", "Group chat energy is unmatched, no cap.",
        ]),
        ("Texting Reply Puns", "These are the quick, punny comebacks that keep a conversation going.", [
            "I can't espresso how much I needed that laugh.", "You've got me feeling grape about today.",
            "That joke was a-peel-ing, send more.", "I'm not lion, that was hilarious.",
            "You butter believe I'm laughing right now.", "That text really made my day, no yolk.",
            "I'm so glad we're texting, it's un-be-leaf-able.", "You're pun-believable, keep them coming.",
        ]),
        ("Late Reply and Everyday Puns", "These fit the everyday chaos of texting, like leaving someone on read.", [
            "Sorry for the late reply, I was busy loafing around.", "My phone battery is as low as my motivation today.",
            "I left you on read because I was bread-th-taking busy.", "I'm not ignoring you, I'm just socially distant from my phone.",
            "My notifications are a mess, just like my life.", "I promise I saw your text, I was just marinating on a reply.",
            "Autocorrect ruined my joke, but I'm still laughing.",
        ]),
    ],
    "how_to_use": "Texting puns are best kept short and casual, perfect for group chat names, quick replies, or a caption when you're sharing a screenshot of a funny conversation.",
    "faq": [
        ("What's a good pun for a group chat name?", "Food and everyday-life puns work especially well for group chat names, since they're relatable and easy for everyone in the chat to get instantly."),
        ("How do I reply to a text with a pun?", "Pick a pun that matches the context of the conversation, whether it's an apology for a late reply or a reaction to something funny someone sent."),
        ("Are texting puns good for captions too?", "Yes, many texting puns double as great captions for screenshots of funny conversations or everyday social media posts."),
    ],
    "related": [("/pun-pickup-lines/", "Pun Pickup Lines"), ("/music-puns/", "Music Puns"), ("/funny-puns/", "Funny Puns")],
}

if __name__ == "__main__":
    print(len(ARTICLES), "articles:", list(ARTICLES.keys()))
