Avazu202 is an open source implementation based on the popular open source tracking tool Prosper 202. We have restructured the code base of Prosper 202, bringing in a DAO layer (Data Access Objects), seperating the business logics of Prosper202 from the data access actions, to make the code more scalable from the development point of view. On top of that we have also migrated the code base from MySQL to MongoDB, so that Avazu202 is cluster-compatible and supported for also high volumes of clicks. 

If you intend to become our publishers or have questions regarding this project, please contact: opensource@avazu.net

Some test results of our implementation:
selenium tests
.1 install -> ok
.2 setup all kind of data(selenium ide, auto test) -> ok

manual tests
.3 test recording clicks
  - of direct link -> ok
  - of simple landing page -> ok
  - of advance landing page -> ok
.4 test overview
  - Campaign Overview -> ok
  - Breakdown Analysis -> ok
  - Day Parting -> ok
  - Week Parting -> ok
  - Group Overview -> ok
.5 test analyze
  - Keywords -> ok
  - Text Ads -> ok
  - Referers -> ok
  - IPs -> ok
  - Landing Pages -> ok
.6 test Visitor History -> ok
.7 test update
  - update CPC -> ok
  - others -> ok
.8 leads and payout -> ok

