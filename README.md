lore
====

Searching the LDAP Directory
----------------------------

**Example:**  Get the first name, last name, and email address of everyone who
has an email address and whose last name begins with S.

	$conn = new Connection('ldap.myhost.com');

	$search = new Query($conn);
	$search
		->attribute('sn')
		->attribute('givenName')
		->attribute('mail')
		->where(
			$search->allOf(array(
				$search->exists('mail'),
				$search->equals('sn', 'S*')
			))
		);

	$result = $search->query();
