@cli
Feature: install, upgrade and uninstall apps that are available in the market-place

  # Note: testing this feature requires that the real market-place be online,
  # working and reachable from the system-under-test.
  # The activity app must not yet be installed on the system-under-test.
  # Happy-path install, upgrade and uninstall are tested all in one scenario as a "user journey"
  # because we need to install anyway, in order to test a normal uninstall.
  Scenario: install, attempt to reinstall, upgrade and uninstall an app that is available in the market-place
    # Note: use the activity app as the example to install
    # it should be an app that is always available
    When the administrator invokes occ command "market:install activity"
    Then the command should have been successful
    And the command output should be:
    """
    activity: Installing new app ...
    activity: App installed.
    """
    And app "activity" should be enabled
    # Attempt to install again and check the different message
    When the administrator invokes occ command "market:install activity"
    Then the command should have been successful
    And the command output should be:
    """
    activity: App already installed and no update available
    """
    And app "activity" should be enabled
    # Attempt to upgrade and check that no update is available
    When the administrator invokes occ command "market:upgrade activity"
    Then the command should have been successful
    And the command output should be:
    """
    activity: No update available.
    """
    And app "activity" should be enabled
    # Uninstall the app - to make sure that uninstall works, and to cleanup
    When the administrator invokes occ command "market:uninstall activity"
    Then the command should have been successful
    And the command output should be:
    """
    activity: Un-Installing ...
    activity: App uninstalled.
    """
    And app "activity" should not be in the apps list

  Scenario: install an app that is not available in the market-place
    When the administrator invokes occ command "market:install nonexistentapp"
    Then the command should have failed with exit code 1
    And the command output should be:
    """
    nonexistentapp: Installing new app ...
    nonexistentapp: Unknown app (nonexistentapp)
    """

  Scenario: upgrade an app that is not available in the market-place
    When the administrator invokes occ command "market:upgrade nonexistentapp"
    Then the command should have failed with exit code 1
    And the command output should be:
    """
    nonexistentapp: Not installed ...
    """

  Scenario: upgrade an app that is available in the market-place but not installed locally
    When the administrator invokes occ command "market:upgrade activity"
    Then the command should have failed with exit code 1
    And the command output should be:
    """
    activity: Not installed ...
    """

  Scenario: uninstall an app that is not available in the market-place
    When the administrator invokes occ command "market:uninstall nonexistentapp"
    Then the command should have failed with exit code 1
    And the command output should be:
    """
    nonexistentapp: Un-Installing ...
    nonexistentapp: App (nonexistentapp) could not be uninstalled. Please check the server logs.
    """

  Scenario: uninstall an app that is available in the market-place but not installed locally
    When the administrator invokes occ command "market:uninstall activity"
    Then the command should have failed with exit code 1
    And the command output should be:
    """
    activity: Un-Installing ...
    activity: App (activity) could not be uninstalled. Please check the server logs.
    """
