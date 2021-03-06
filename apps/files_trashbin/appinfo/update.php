<?php
/**
 * @author Björn Schießle <schiessle@owncloud.com>
 * @author Christopher Schäpers <kondou@ts.unde.re>
 * @author Thomas Müller <thomas.mueller@tmit.eu>
 *
 * @copyright Copyright (c) 2015, ownCloud, Inc.
 * @license AGPL-3.0
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License, version 3,
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 *
 */

$config = \OC::$server->getConfig();
$installedVersion = $config->getAppValue('files_trashbin', 'installed_version');

if (version_compare($installedVersion, '0.6', '<')) {
	//size of the trash bin could be incorrect, remove it for all users to
	//enforce a recalculation during next usage.
	\OC_DB::dropTable('files_trashsize');
}

if (version_compare($installedVersion, '0.6.4', '<')) {
	$isExpirationEnabled = $config->getSystemValue('trashbin_auto_expire', true);
	$oldObligation = $config->getSystemValue('trashbin_retention_obligation', null);

	$newObligation = 'auto';
	if ($isExpirationEnabled) {
		if (!is_null($oldObligation)) {
			$newObligation = strval($oldObligation) . ', auto';
		}
	} else {
		$newObligation = 'disabled';
	}

	$config->setSystemValue('trashbin_retention_obligation', $newObligation);
	$config->deleteSystemValue('trashbin_auto_expire');
}
