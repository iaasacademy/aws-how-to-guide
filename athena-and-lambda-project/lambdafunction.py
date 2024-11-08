
import json
import boto3
import time
from datetime import datetime

# Initialize Athena and S3 clients
athena_client = boto3.client('athena')
s3_client = boto3.client('s3')
s3_bucket_name = "[bellybrewanalysis1]"
database_name = "[bellybrewanalysis_db]"

def create_table():
    """Create the raw_externals table if it does not exist."""
    query = f"""
        CREATE EXTERNAL TABLE IF NOT EXISTS {database_name}.raw_data (
            CaskID string,
            CaskName string,
            CaskType string,
            KombuchaFlavor string,
            Lactobacillus bigint,
            Acetobacter bigint,
            Gluconobacter bigint,
            `PH Level` double,
            `LS-Code` string,
            Weight double,
            Temperature double
        )
        ROW FORMAT SERDE 'org.apache.hadoop.hive.serde2.OpenCSVSerde'
        WITH SERDEPROPERTIES (
            'separatorChar' = ',',
            'quoteChar' = '"'
        )
        STORED AS TEXTFILE
        LOCATION 's3://{s3_bucket_name}/raw/'
        TBLPROPERTIES ('has_encrypted_data'='false',
            'skip.header.line.count' = '1'
        );
    """
    # Start the Athena query
    response = athena_client.start_query_execution(
        QueryString=query,
        QueryExecutionContext={'Database': database_name},
        ResultConfiguration={'OutputLocation': f's3://{s3_bucket_name}/athena/'}
    )
    
    # Get the execution ID for the query
    query_execution_id = response['QueryExecutionId']
    
    # Wait for the query to complete
    query_status = None
    while query_status != 'SUCCEEDED':
        query_status_response = athena_client.get_query_execution(QueryExecutionId=query_execution_id)
        query_status = query_status_response['QueryExecution']['Status']['State']
        
        if query_status == 'FAILED':
            raise Exception('Query failed to run.')
        elif query_status == 'CANCELLED':
            raise Exception('Query was cancelled.')

        # Sleep for a few seconds before polling again
        time.sleep(5)
    return

def lambda_handler(event, context):

    current_date = datetime.now().strftime("%Y-%m-%d")
    create_table()
    query = f"""
        SELECT * FROM "{database_name}"."raw_data"
        WHERE ("PH Level" < 2.5 or "PH Level" > 3.5)
        AND "$path" like '%{current_date}%';
        """
    
    # Start the Athena query
    response = athena_client.start_query_execution(
        QueryString=query,
        QueryExecutionContext={'Database': database_name},
        ResultConfiguration={'OutputLocation': f's3://{s3_bucket_name}/processed/{current_date}/'}
    )
    
    # Get the execution ID for the query
    query_execution_id = response['QueryExecutionId']
    
    # Wait for the query to complete
    query_status = None
    while query_status != 'SUCCEEDED':
        query_status_response = athena_client.get_query_execution(QueryExecutionId=query_execution_id)
        query_status = query_status_response['QueryExecution']['Status']['State']
        
        if query_status == 'FAILED':
            raise Exception('Query failed to run.')
        elif query_status == 'CANCELLED':
            raise Exception('Query was cancelled.')

        # Sleep for a few seconds before polling again
        time.sleep(5)
    
    # Now we can retrieve the location of the results.
    result_s3_location = f's3://{s3_bucket_name}/processed/{current_date}/' + query_execution_id + '.csv'
    
    return {
        'statusCode': 200,
        'body': json.dumps({
            'message': 'Query executed successfully!',
            'result_s3_location': result_s3_location
        })
    }

