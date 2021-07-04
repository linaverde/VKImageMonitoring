import json
from dostoevsky.tokenization import RegexTokenizer
from dostoevsky.models import FastTextSocialNetworkModel
from argparse import ArgumentParser


def main():

    p = ArgumentParser(description="Get mood from message")
    p.add_argument('message', type=str, help="Message text")
    options = p.parse_args()
    message = options.message
    if message is None:
        print("No message recieved")

    tokenizer = RegexTokenizer()
    model = FastTextSocialNetworkModel(tokenizer=tokenizer)

    messages = [
        message
    ]

    results = model.predict(messages, k=2)
    for message, sentiment in zip(messages, results):
        print(json.dumps(sentiment))


if __name__ == '__main__':
    main()
