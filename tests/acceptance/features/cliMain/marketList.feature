@cli
Feature: list apps that are available in the market-place

  # Note: testing this feature requires that the real market-place be online,
  # working and reachable from the system-under-test.
  Scenario: list the apps available in the market-place
    When the administrator invokes occ command "market:list"
    Then the command should have been successful
    # The command lists all the apps that are available on the market-place
    # Just check for an example app that should always be there
    And the command output should contain the text "activity"
