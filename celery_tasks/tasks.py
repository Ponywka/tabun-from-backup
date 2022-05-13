from os import environ
import smtplib

from email.header import Header
from email.utils import formataddr
from email.utils import formatdate
from email.utils import COMMASPACE
from email.mime.multipart import MIMEMultipart
from email.mime.text import MIMEText

from bs4 import BeautifulSoup
from celery.decorators import task
from elasticsearch import Elasticsearch

es = Elasticsearch('http://elastic:9200')

SMTP_HOST = environ.get("CELERY_MAILER_HOST")
SMTP_PORT = environ.get("CELERY_MAILER_PORT")


@task
def send_mail(**kwargs):
    assert SMTP_HOST
    assert SMTP_PORT

    recipients = kwargs.get('recipients')

    from_email = kwargs.get('from_email')
    from_name = kwargs.get('from_name')

    subject = kwargs.get('subject')
    body = kwargs.get('body')

    is_html = kwargs.get('is_html')

    message = MIMEMultipart('alternative')

    message['Date'] = formatdate(localtime=1)
    message['Subject'] = Header(subject, 'utf-8')
    message['From'] = formataddr([from_name, from_email])
    message['To'] = COMMASPACE.join(map(formataddr, recipients))

    soup = BeautifulSoup(body)
    plain_body = soup.getText()

    part1 = MIMEText(plain_body.encode('utf-8'), 'plain', 'utf-8')
    message.attach(part1)

    if is_html:
        part2 = MIMEText(body.encode('utf-8'), 'html', 'utf-8')
        message.attach(part2)

    server = smtplib.SMTP(SMTP_HOST, int(SMTP_PORT))
    server.sendmail(
        from_email,
        [pair[1] for pair in recipients],
        message.as_string()
    )
    server.quit()


@task
def topic_index(**kwargs):
    topic_id = kwargs.get('topic_id')
    topic_blog_id = kwargs.get('topic_blog_id')
    topic_user_id = kwargs.get('topic_user_id')
    topic_type = kwargs.get('topic_type')
    topic_title = kwargs.get('topic_title')
    topic_text = kwargs.get('topic_text')
    topic_tags = kwargs.get('topic_tags')
    topic_date = kwargs.get('topic_date')
    topic_publish = kwargs.get('topic_publish')

    topic_tags = topic_tags.split(',')

    doc = {
        'blog_id': int(topic_blog_id),
        'user_id': int(topic_user_id),
        'type': topic_type,
        'title': topic_title.strip(),
        'text': topic_text.strip(),
        'tags': topic_tags,
        'date': topic_date,
        'publish': topic_publish
    }
    es.index(index='topic', id=int(topic_id), document=doc)

@task
def topic_updateblog(**kwargs):
    topic_id = kwargs.get('topic_id')
    topic_blog_id = kwargs.get('topic_blog_id')

    doc = {
        'doc': {
            'blog_id': int(topic_blog_id)
        }
    }
    es.update(index='topic', id=int(topic_id), body=doc)



@task
def topic_delete(**kwargs):
    topic_id = kwargs.get('topic_id')

    es.delete(index='topic', id=int(topic_id))


@task
def comment_index(**kwargs):
    comment_id = kwargs.get('comment_id')
    comment_target_id = kwargs.get('comment_target_id')
    comment_blog_id = kwargs.get('comment_blog_id')
    comment_target_type = kwargs.get('comment_target_type')
    comment_user_id = kwargs.get('comment_user_id')
    comment_text = kwargs.get('comment_text')
    comment_date = kwargs.get('comment_date')
    comment_publish = kwargs.get('comment_publish')

    doc = {
        'blog_id': int(comment_blog_id),
        'target_id': int(comment_target_id),
        'target_type': comment_target_type,
        'user_id': int(comment_user_id),
        'text': comment_text.strip(),
        'date': comment_date,
        'publish': comment_publish
    }
    es.index(index='comment', id=int(comment_id), body=doc)


@task
def comment_updateblog(**kwargs):
    comment_id = kwargs.get('comment_id')
    comment_blog_id = kwargs.get('comment_blog_id')

    doc = {
        'doc': {
            'blog_id': int(comment_blog_id)
        }
    }
    es.update(index='comment', id=int(comment_id), body=doc)


@task
def comment_delete(**kwargs):
    comment_id = kwargs.get('comment_id')

    es.delete(index='comment', id=int(comment_id))
