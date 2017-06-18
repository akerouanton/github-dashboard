Feature: Ingest an event sent by Github
  In order to extract value from the data
  As a product operator
  I need the software to ingest fresh github event

  Scenario: I send a payload with a valid signature
    Given I have a json payload representing a raw event
      And I have a valid signature
     When I send it to the github hook
     Then I should receive a 200 response

  Scenario: I send a payload with an invalid signature
    Given I have a json payload representing a raw event
      And I have an invalid signature
     When I send it to the github hook
     Then I should receive a 400 response
