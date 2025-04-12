# ALB Setup and Path-Based Routing

This document guides you through setting up an Application Load Balancer (ALB) and configuring path-based routing.

## Steps:

1. **Launch EC2 Instances**:  
   - Use the provided `server1_userdata.sh` and `server2_userdata.sh` scripts to launch your EC2 instances.
2. **Create a Target Group**:  
   - Register your instances in a Target Group from the EC2 console.
3. **Create an ALB**:
   - Set the ALB as internet-facing.
   - Choose subnets in different Availability Zones.
   - Configure a listener on port 80.
4. **Configure Path-Based Routing**:
   - In the ALB listener rules, add conditions for paths like `/app1/*` or `/app2/*` to direct traffic to specific targets.
5. **Testing**:
   - Verify by accessing `http://<ALB-DNS>/app1/` and `http://<ALB-DNS>/app2/` to see the different content.
