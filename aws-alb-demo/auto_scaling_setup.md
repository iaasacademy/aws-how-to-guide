# Auto Scaling Setup

This document outlines how to configure an Auto Scaling Group (ASG) to start with two instances and automatically add a third instance upon increased load.

## Steps:

1. **Create a Launch Template/Configuration**:
   - Use one of the provided user data scripts.
   - Specify your AMI, instance type, and other configurations.
2. **Create an Auto Scaling Group (ASG)**:
   - Set the desired capacity to 2 and a maximum capacity of 3.
   - Attach the ASG to the previously created Target Group.
3. **Configure Scaling Policies**:
   - Use CloudWatch alarms (e.g., on CPU utilization) to trigger scale-out events.
4. **Testing**:
   - Simulate load to ensure the ASG automatically launches an additional instance.
