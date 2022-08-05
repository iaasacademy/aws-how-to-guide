import os
import boto3
import logging

DEFAULT_TAGS = os.environ.get("DEFAULT_TAGS")
print("DEFAULT_TAGS", DEFAULT_TAGS)

logger = logging.getLogger()
level = logging.getLevelName(os.environ.get("LOG_LEVEL", "INFO"))
print("Logging level -- ", level)
logger.setLevel(level)

ec2_resource = boto3.resource('ec2')
ec2_client = boto3.client('ec2')
    
def lambda_handler(event, context):
    """
        Function that start and stop ec2 instances schedule and with specific tags<br/>
        :param event: Input event, that should contain action and tags parameters, where tags is a list of comma separates key/value tags.<br/>
        :param context: Lambda context.<br/>
        :return: nothing
    """
    logger.debug(event)

    print("event -- ", event)

    tags = get_tags(event['tags'] if 'tags' in event else DEFAULT_TAGS)
    print("tags -- ", tags)
    instances = get_instances_by_tags(tags)

    if not instances:
        logger.warning('No instances available with this tags')
    else:
        if event['action'] == 'start':
            ec2_client.start_instances(InstanceIds=instances)
            logger.info('Starting instances.')
        elif event['action'] == 'stop':
            ec2_client.stop_instances(InstanceIds=instances)
            logger.info('Stopping instances.')
        else:
            logger.warning('No instances availables with this tags')


def get_tags(tags):
    """
        Method that split comma separated tags and return a formed tags filter<br/>
        :param tags: Comma separated string with the tags values.<br/>
        :return: tags structure
    """
    final_tags = []
    split_tags = tags.split(",")
    for tag in split_tags:
        values = tag.split('=')
        final_tags.append({
            'Name': values[0],
            'Values': [values[1]]
        })
    return final_tags


def get_instances_by_tags(tags):
    """
        Method that filter all ec2 instances and return only the instances with specific tags<br/>
        :param tags: Filter structure with tag values.<br/>
        :return: list of ec2 instances
    """
    response = ec2_resource.instances.filter(Filters=tags)
    print("Response -- ", response)
    for instance in response:
        print("Instance -- ", instance)
    intance_ids = [instance.id for instance in response]
    print("intance_ids -- ", intance_ids)

    return intance_ids