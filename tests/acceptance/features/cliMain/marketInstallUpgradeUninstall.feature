@cli
Feature: install, upgrade and uninstall apps that are available in the market-place

  # Note: testing this feature requires that the real market-place be online,
  # working and reachable from the system-under-test.
  # The migrate_to_ocis app must not yet be installed on the system-under-test.
  # Happy-path install, upgrade and uninstall are tested all in one scenario as a "user journey"
  # because we need to install anyway, in order to test a normal uninstall.
  Scenario: install, attempt to reinstall, upgrade and uninstall an app that is available in the market-place
    # Note: use the migrate_to_ocis app as the example to install
    # it should be an app that is always available
    When the administrator invokes occ command "market:install migrate_to_ocis"
    Then the command should have been successful
    And the command output should be:
    """
    migrate_to_ocis: Installing new app ...
    migrate_to_ocis: App installed.
    """
    And app "migrate_to_ocis" should be enabled
    # Attempt to install again and check the different message
    When the administrator invokes occ command "market:install migrate_to_ocis"
    Then the command should have been successful
    And the command output should be:
    """
    migrate_to_ocis: App already installed and no update available
    """
    And app "migrate_to_ocis" should be enabled
    # Attempt to upgrade and check that no update is available
    When the administrator invokes occ command "market:upgrade migrate_to_ocis"
    Then the command should have been successful
    And the command output should be:
    """
    migrate_to_ocis: No update available.
    """
    And app "migrate_to_ocis" should be enabled
    # Uninstall the app - to make sure that uninstall works, and to cleanup
    When the administrator invokes occ command "market:uninstall migrate_to_ocis"
    Then the command should have been successful
    And the command output should be:
    """
    migrate_to_ocis: Un-Installing ...
    migrate_to_ocis: App uninstalled.
    """
    And app "migrate_to_ocis" should not be in the apps list


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
    When the administrator invokes occ command "market:upgrade migrate_to_ocis"
    Then the command should have failed with exit code 1
    And the command output should be:
    """
    migrate_to_ocis: Not installed ...
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
    When the administrator invokes occ command "market:uninstall migrate_to_ocis"
    Then the command should have failed with exit code 1
    And the command output should be:
    """
    migrate_to_ocis: Un-Installing ...
    migrate_to_ocis: App (migrate_to_ocis) could not be uninstalled. Please check the server logs.
    """
