<?php
namespace MaxServ\Tikafal\Service;

/**
 *  Copyright notice
 *
 *  ⓒ 2015 Michiel Roos <michiel@maxserv.com>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is free
 *  software; you can redistribute it and/or modify it under the terms of the
 *  GNU General Public License as published by the Free Software Foundation;
 *  either version 2 of the License, or (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful, but
 *  WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 *  or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for
 *  more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 */

use TYPO3\CMS\Core\Resource;
use TYPO3\CMS\Core\Utility\CommandUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;


/**
 * This is a bridge service between TYPO3 metadata extraction services
 * and FAL extractors for Local Driver.
 *
 * @category    Service
 * @package     TYPO3
 * @subpackage  tx_extractor
 * @author Michiel Roos <michiel@maxserv.com>
 * @license     http://www.gnu.org/copyleft/gpl.html
 */
class Tika implements \TYPO3\CMS\Core\Resource\Index\ExtractorInterface {

	/**
	 * Extension configuration
	 *
	 * @var array
	 */
	protected $configuration;

	/**
	 * Metadata field mapping
	 * key = database field
	 * value = comma separated list of extracted fields
	 *
	 * @var array
	 */
	protected $fieldmap;

	/**
	 * Configuration manager
	 *
	 * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
	 * @inject
	 */
	protected $configurationManager;

	/**
	 * Extension settings
	 *
	 * @var array
	 */
	protected $settings;

	/**
	 * Supported mime types
	 *
	 * @var array
	 */
	protected $supporteMimeTypes = array(
		'application/activemessage',
		'application/andrew-inset',
		'application/applefile',
		'application/applixware',
		'application/atomcat+xml',
		'application/atomicmail',
		'application/atomsvc+xml',
		'application/atom+xml',
		'application/auth-policy+xml',
		'application/batch-smtp',
		'application/beep+xml',
		'application/bizagi-modeler',
		'application/cals-1840',
		'application/cbor',
		'application/ccxml+xml',
		'application/cea-2018+xml',
		'application/cellml+xml',
		'application/cnrp+xml',
		'application/commonground',
		'application/conference-info+xml',
		'application/cpl+xml',
		'application/cstadata+xml',
		'application/csta+xml',
		'application/cu-seeme',
		'application/cybercash',
		'application/davmount+xml',
		'application/dca-rft',
		'application/dec-dx',
		'application/dialog-info+xml',
		'application/dicom',
		'application/dita+xml',
		'application/dita+xml; format=concept',
		'application/dita+xml; format=map',
		'application/dita+xml; format=task',
		'application/dita+xml; format=topic',
		'application/dita+xml; format=val',
		'application/dns',
		'application/dvcs',
		'application/ecmascript',
		'application/edi-consent',
		'application/edifact',
		'application/edi-x12',
		'application/emma+xml',
		'application/epp+xml',
		'application/epub+zip',
		'application/eshop',
		'application/example',
		'application/fastinfoset',
		'application/fastsoap',
		'application/fits',
		'application/font-tdpfr',
		'application/gzip',
		'application/h224',
		'application/http',
		'application/hyperstudio',
		'application/ibe-key-request+xml',
		'application/ibe-pkg-reply+xml',
		'application/ibe-pp-data',
		'application/iges',
		'application/illustrator',
		'application/im-iscomposing+xml',
		'application/index',
		'application/index.cmd',
		'application/index.obj',
		'application/index.response',
		'application/index.vnd',
		'application/inf',
		'application/iotp',
		'application/ipp',
		'application/isup',
		'application/java-archive',
		'application/javascript',
		'application/java-serialized-object',
		'application/java-vm',
		'application/json',
		'application/kate',
		'application/kpml-request+xml',
		'application/kpml-response+xml',
		'application/lost+xml',
		'application/mac-binhex40',
		'application/mac-compactpro',
		'application/macwriteii',
		'application/marc',
		'application/mathematica',
		'application/mathml+xml',
		'application/mbms-associated-procedure-description+xml',
		'application/mbms-deregister+xml',
		'application/mbms-envelope+xml',
		'application/mbms-msk-response+xml',
		'application/mbms-msk+xml',
		'application/mbms-protection-description+xml',
		'application/mbms-reception-report+xml',
		'application/mbms-register-response+xml',
		'application/mbms-register+xml',
		'application/mbms-user-service-description+xml',
		'application/mbox',
		'application/media_control+xml',
		'application/mediaservercontrol+xml',
		'application/mikey',
		'application/mosskey-data',
		'application/mosskey-request',
		'application/moss-keys',
		'application/moss-signature',
		'application/mp4',
		'application/mpeg4-generic',
		'application/mpeg4-iod',
		'application/mpeg4-iod-xmt',
		'application/msword',
		'application/msword2',
		'application/msword5',
		'application/mxf',
		'application/nasdata',
		'application/news-checkgroups',
		'application/news-groupinfo',
		'application/news-transmission',
		'application/nss',
		'application/ocsp-request',
		'application/ocsp-response',
		'application/octet-stream',
		'application/oda',
		'application/oebps-package+xml',
		'application/ogg',
		'application/onenote',
		'application/parityfec',
		'application/patch-ops-error+xml',
		'application/pdf',
		'application/pgp-encrypted',
		'application/pgp-keys',
		'application/pgp-signature',
		'application/pics-rules',
		'application/pidf-diff+xml',
		'application/pidf+xml',
		'application/pkcs10',
		'application/pkcs7-mime',
		'application/pkcs7-signature',
		'application/pkix-cert',
		'application/pkixcmp',
		'application/pkix-crl',
		'application/pkix-pkipath',
		'application/pls+xml',
		'application/poc-settings+xml',
		'application/postscript',
		'application/prs.alvestrand.titrax-sheet',
		'application/prs.cww',
		'application/prs.nprend',
		'application/prs.plucker',
		'application/qsig',
		'application/rdf+xml',
		'application/reginfo+xml',
		'application/relax-ng-compact-syntax',
		'application/remote-printing',
		'application/resource-lists-diff+xml',
		'application/resource-lists+xml',
		'application/riscos',
		'application/rlmi+xml',
		'application/rls-services+xml',
		'application/rsd+xml',
		'application/rss+xml',
		'application/rtf',
		'application/rtx',
		'application/samlassertion+xml',
		'application/samlmetadata+xml',
		'application/sbml+xml',
		'application/scvp-cv-request',
		'application/scvp-cv-response',
		'application/scvp-vp-request',
		'application/scvp-vp-response',
		'application/sdp',
		'application/sereal',
		'application/sereal; version=1',
		'application/sereal; version=2',
		'application/sereal; version=3',
		'application/set-payment',
		'application/set-payment-initiation',
		'application/set-registration',
		'application/set-registration-initiation',
		'application/sgml',
		'application/sgml-open-catalog',
		'application/shf+xml',
		'application/sieve',
		'application/simple-filter+xml',
		'application/simple-message-summary',
		'application/simplesymbolcontainer',
		'application/slate',
		'application/sldworks',
		'application/smil+xml',
		'application/soap+fastinfoset',
		'application/soap+xml',
		'application/sparql-query',
		'application/sparql-results+xml',
		'application/spirits-event+xml',
		'application/srgs',
		'application/srgs+xml',
		'application/ssml+xml',
		'application/timestamp-query',
		'application/timestamp-reply',
		'application/tve-trigger',
		'application/ulpfec',
		'application/vemmi',
		'application/vividence.scriptfile',
		'application/vnd.3gpp2.bcmcsinfo+xml',
		'application/vnd.3gpp2.sms',
		'application/vnd.3gpp2.tcap',
		'application/vnd.3gpp.bsf+xml',
		'application/vnd.3gpp.pic-bw-large',
		'application/vnd.3gpp.pic-bw-small',
		'application/vnd.3gpp.pic-bw-var',
		'application/vnd.3gpp.sms',
		'application/vnd.3m.post-it-notes',
		'application/vnd.accpac.simply.aso',
		'application/vnd.accpac.simply.imp',
		'application/vnd.acucobol',
		'application/vnd.acucorp',
		'application/vnd.adobe.aftereffects.project',
		'application/vnd.adobe.aftereffects.template',
		'application/vnd.adobe.air-application-installer-package+zip',
		'application/vnd.adobe.xdp+xml',
		'application/vnd.adobe.xfdf',
		'application/vnd.aether.imp',
		'application/vnd.airzip.filesecure.azf',
		'application/vnd.airzip.filesecure.azs',
		'application/vnd.amazon.ebook',
		'application/vnd.americandynamics.acc',
		'application/vnd.amiga.ami',
		'application/vnd.android.package-archive',
		'application/vnd.anser-web-certificate-issue-initiation',
		'application/vnd.anser-web-funds-transfer-initiation',
		'application/vnd.antix.game-component',
		'application/vnd.apple.installer+xml',
		'application/vnd.apple.iwork',
		'application/vnd.apple.keynote',
		'application/vnd.apple.numbers',
		'application/vnd.apple.pages',
		'application/vnd.arastra.swi',
		'application/vnd.audiograph',
		'application/vnd.autopackage',
		'application/vnd.avistar+xml',
		'application/vnd.blueice.multipass',
		'application/vnd.bluetooth.ep.oob',
		'application/vnd.bmi',
		'application/vnd.businessobjects',
		'application/vnd.cab-jscript',
		'application/vnd.canon-cpdl',
		'application/vnd.canon-lips',
		'application/vnd.cendio.thinlinc.clientconf',
		'application/vnd.chemdraw+xml',
		'application/vnd.chipnuts.karaoke-mmd',
		'application/vnd.cinderella',
		'application/vnd.cirpack.isdn-ext',
		'application/vnd.claymore',
		'application/vnd.clonk.c4group',
		'application/vnd.commerce-battelle',
		'application/vnd.commonspace',
		'application/vnd.contact.cmsg',
		'application/vnd.cosmocaller',
		'application/vnd.crick.clicker',
		'application/vnd.crick.clicker.keyboard',
		'application/vnd.crick.clicker.palette',
		'application/vnd.crick.clicker.template',
		'application/vnd.crick.clicker.wordbank',
		'application/vnd.criticaltools.wbs+xml',
		'application/vnd.ctc-posml',
		'application/vnd.ctct.ws+xml',
		'application/vnd.cups-pdf',
		'application/vnd.cups-postscript',
		'application/vnd.cups-ppd',
		'application/vnd.cups-raster',
		'application/vnd.cups-raw',
		'application/vnd.curl.car',
		'application/vnd.curl.pcurl',
		'application/vnd.cybank',
		'application/vnd.data-vision.rdz',
		'application/vnd.denovo.fcselayout-link',
		'application/vnd.dir-bi.plate-dl-nosuffix',
		'application/vnd.dna',
		'application/vnd.dolby.mlp',
		'application/vnd.dolby.mobile.1',
		'application/vnd.dolby.mobile.2',
		'application/vnd.dpgraph',
		'application/vnd.dreamfactory',
		'application/vnd.dvb.esgcontainer',
		'application/vnd.dvb.ipdcdftnotifaccess',
		'application/vnd.dvb.ipdcesgaccess',
		'application/vnd.dvb.ipdcroaming',
		'application/vnd.dvb.iptv.alfec-base',
		'application/vnd.dvb.iptv.alfec-enhancement',
		'application/vnd.dvb.notif-aggregate-root+xml',
		'application/vnd.dvb.notif-container+xml',
		'application/vnd.dvb.notif-generic+xml',
		'application/vnd.dvb.notif-ia-msglist+xml',
		'application/vnd.dvb.notif-ia-registration-request+xml',
		'application/vnd.dvb.notif-ia-registration-response+xml',
		'application/vnd.dvb.notif-init+xml',
		'application/vnd.dxr',
		'application/vnd.dynageo',
		'application/vnd.ecdis-update',
		'application/vnd.ecowin.chart',
		'application/vnd.ecowin.filerequest',
		'application/vnd.ecowin.fileupdate',
		'application/vnd.ecowin.series',
		'application/vnd.ecowin.seriesrequest',
		'application/vnd.ecowin.seriesupdate',
		'application/vnd.emclient.accessrequest+xml',
		'application/vnd.enliven',
		'application/vnd.epson.esf',
		'application/vnd.epson.msf',
		'application/vnd.epson.quickanime',
		'application/vnd.epson.salt',
		'application/vnd.epson.ssf',
		'application/vnd.ericsson.quickcall',
		'application/vnd.eszigno3+xml',
		'application/vnd.etsi.aoc+xml',
		'application/vnd.etsi.cug+xml',
		'application/vnd.etsi.iptvcommand+xml',
		'application/vnd.etsi.iptvdiscovery+xml',
		'application/vnd.etsi.iptvprofile+xml',
		'application/vnd.etsi.iptvsad-bc+xml',
		'application/vnd.etsi.iptvsad-cod+xml',
		'application/vnd.etsi.iptvsad-npvr+xml',
		'application/vnd.etsi.iptvueprofile+xml',
		'application/vnd.etsi.mcid+xml',
		'application/vnd.etsi.sci+xml',
		'application/vnd.etsi.simservs+xml',
		'application/vnd.eudora.data',
		'application/vnd.ezpix-album',
		'application/vnd.ezpix-package',
		'application/vnd.fdf',
		'application/vnd.fdsn.mseed',
		'application/vnd.fdsn.seed',
		'application/vnd.ffsns',
		'application/vnd.fints',
		'application/vnd.flographit',
		'application/vnd.fluxtime.clip',
		'application/vnd.font-fontforge-sfd',
		'application/vnd.framemaker',
		'application/vnd.frogans.fnc',
		'application/vnd.frogans.ltf',
		'application/vnd.fsc.weblaunch',
		'application/vnd.f-secure.mobile',
		'application/vnd.fujitsu.oasys',
		'application/vnd.fujitsu.oasys2',
		'application/vnd.fujitsu.oasys3',
		'application/vnd.fujitsu.oasysgp',
		'application/vnd.fujitsu.oasysprs',
		'application/vnd.fujixerox.art4',
		'application/vnd.fujixerox.art-ex',
		'application/vnd.fujixerox.ddd',
		'application/vnd.fujixerox.docuworks',
		'application/vnd.fujixerox.docuworks.binder',
		'application/vnd.fujixerox.hbpl',
		'application/vnd.fut-misnet',
		'application/vnd.fuzzysheet',
		'application/vnd.genomatix.tuxedo',
		'application/vnd.geogebra.file',
		'application/vnd.geogebra.tool',
		'application/vnd.geometry-explorer',
		'application/vnd.gmx',
		'application/vnd.google-earth.kml+xml',
		'application/vnd.google-earth.kmz',
		'application/vnd.grafeq',
		'application/vnd.gridmp',
		'application/vnd.groove-account',
		'application/vnd.groove-help',
		'application/vnd.groove-identity-message',
		'application/vnd.groove-injector',
		'application/vnd.groove-tool-message',
		'application/vnd.groove-tool-template',
		'application/vnd.groove-vcard',
		'application/vnd.handheld-entertainment+xml',
		'application/vnd.hbci',
		'application/vnd.hcl-bireports',
		'application/vnd.hhe.lesson-player',
		'application/vnd.hp-hpgl',
		'application/vnd.hp-hpid',
		'application/vnd.hp-hps',
		'application/vnd.hp-jlyt',
		'application/vnd.hp-pcl',
		'application/vnd.hp-pclxl',
		'application/vnd.httphone',
		'application/vnd.hydrostatix.sof-data',
		'application/vnd.hzn-3d-crossword',
		'application/vnd.ibm.afplinedata',
		'application/vnd.ibm.electronic-media',
		'application/vnd.ibm.minipay',
		'application/vnd.ibm.modcap',
		'application/vnd.ibm.rights-management',
		'application/vnd.ibm.secure-container',
		'application/vnd.iccprofile',
		'application/vnd.igloader',
		'application/vnd.immervision-ivp',
		'application/vnd.immervision-ivu',
		'application/vnd.informedcontrol.rms+xml',
		'application/vnd.informix-visionary',
		'application/vnd.intercon.formnet',
		'application/vnd.intertrust.digibox',
		'application/vnd.intertrust.nncp',
		'application/vnd.intu.qbo',
		'application/vnd.intu.qfx',
		'application/vnd.iptc.g2.conceptitem+xml',
		'application/vnd.iptc.g2.knowledgeitem+xml',
		'application/vnd.iptc.g2.newsitem+xml',
		'application/vnd.iptc.g2.packageitem+xml',
		'application/vnd.ipunplugged.rcprofile',
		'application/vnd.irepository.package+xml',
		'application/vnd.is-xpr',
		'application/vnd.jam',
		'application/vnd.japannet-directory-service',
		'application/vnd.japannet-jpnstore-wakeup',
		'application/vnd.japannet-payment-wakeup',
		'application/vnd.japannet-registration',
		'application/vnd.japannet-registration-wakeup',
		'application/vnd.japannet-setstore-wakeup',
		'application/vnd.japannet-verification',
		'application/vnd.japannet-verification-wakeup',
		'application/vnd.jcp.javame.midlet-rms',
		'application/vnd.jisp',
		'application/vnd.joost.joda-archive',
		'application/vnd.kahootz',
		'application/vnd.kde.karbon',
		'application/vnd.kde.kchart',
		'application/vnd.kde.kformula',
		'application/vnd.kde.kivio',
		'application/vnd.kde.kontour',
		'application/vnd.kde.kpresenter',
		'application/vnd.kde.kspread',
		'application/vnd.kde.kword',
		'application/vnd.kenameaapp',
		'application/vnd.kidspiration',
		'application/vnd.kinar',
		'application/vnd.koan',
		'application/vnd.kodak-descriptor',
		'application/vnd.liberty-request+xml',
		'application/vnd.llamagraphics.life-balance.desktop',
		'application/vnd.llamagraphics.life-balance.exchange+xml',
		'application/vnd.lotus-1-2-3',
		'application/vnd.lotus-approach',
		'application/vnd.lotus-freelance',
		'application/vnd.lotus-notes',
		'application/vnd.lotus-organizer',
		'application/vnd.lotus-screencam',
		'application/vnd.lotus-wordpro',
		'application/vnd.macports.portpkg',
		'application/vnd.marlin.drm.actiontoken+xml',
		'application/vnd.marlin.drm.conftoken+xml',
		'application/vnd.marlin.drm.license+xml',
		'application/vnd.marlin.drm.mdcf',
		'application/vnd.mcd',
		'application/vnd.medcalcdata',
		'application/vnd.mediastation.cdkey',
		'application/vnd.meridian-slingshot',
		'application/vnd.mfer',
		'application/vnd.mfmp',
		'application/vnd.micrografx.flo',
		'application/vnd.micrografx.igx',
		'application/vnd.mif',
		'application/vnd.mindjet.mindmanager',
		'application/vnd.minisoft-hp3000-save',
		'application/vnd.mitsubishi.misty-guard.trustweb',
		'application/vnd.mobius.daf',
		'application/vnd.mobius.dis',
		'application/vnd.mobius.mbk',
		'application/vnd.mobius.mqy',
		'application/vnd.mobius.msl',
		'application/vnd.mobius.plc',
		'application/vnd.mobius.txf',
		'application/vnd.mophun.application',
		'application/vnd.mophun.certificate',
		'application/vnd.motorola.flexsuite',
		'application/vnd.motorola.flexsuite.adsi',
		'application/vnd.motorola.flexsuite.fis',
		'application/vnd.motorola.flexsuite.gotap',
		'application/vnd.motorola.flexsuite.kmr',
		'application/vnd.motorola.flexsuite.ttc',
		'application/vnd.motorola.flexsuite.wem',
		'application/vnd.motorola.iprm',
		'application/vnd.mozilla.xul+xml',
		'application/vnd.ms-artgalry',
		'application/vnd.ms-asf',
		'application/vnd.ms-cab-compressed',
		'application/vnd.mseq',
		'application/vnd.ms-excel',
		'application/vnd.ms-excel.addin.macroenabled.12',
		'application/vnd.ms-excel.sheet.2',
		'application/vnd.ms-excel.sheet.3',
		'application/vnd.ms-excel.sheet.4',
		'application/vnd.ms-excel.sheet.binary.macroenabled.12',
		'application/vnd.ms-excel.sheet.macroenabled.12',
		'application/vnd.ms-excel.template.macroenabled.12',
		'application/vnd.ms-excel.workspace.3',
		'application/vnd.ms-excel.workspace.4',
		'application/vnd.ms-fontobject',
		'application/vnd.ms-htmlhelp',
		'application/vnd.msign',
		'application/vnd.ms-ims',
		'application/vnd.ms-lrm',
		'application/vnd.ms-outlook',
		'application/vnd.ms-outlook-pst',
		'application/vnd.ms-pki.seccat',
		'application/vnd.ms-pki.stl',
		'application/vnd.ms-playready.initiator+xml',
		'application/vnd.ms-powerpoint',
		'application/vnd.ms-powerpoint.addin.macroenabled.12',
		'application/vnd.ms-powerpoint.presentation.macroenabled.12',
		'application/vnd.ms-powerpoint.slide.macroenabled.12',
		'application/vnd.ms-powerpoint.slideshow.macroenabled.12',
		'application/vnd.ms-powerpoint.template.macroenabled.12',
		'application/vnd.ms-project',
		'application/vnd.ms-tnef',
		'application/vnd.ms-visio.drawing',
		'application/vnd.ms-visio.drawing.macroenabled.12',
		'application/vnd.ms-visio.stencil',
		'application/vnd.ms-visio.stencil.macroenabled.12',
		'application/vnd.ms-visio.template',
		'application/vnd.ms-visio.template.macroenabled.12',
		'application/vnd.ms-wmdrm.lic-chlg-req',
		'application/vnd.ms-wmdrm.lic-resp',
		'application/vnd.ms-wmdrm.meter-chlg-req',
		'application/vnd.ms-wmdrm.meter-resp',
		'application/vnd.ms-word.document.macroenabled.12',
		'application/vnd.ms-word.template.macroenabled.12',
		'application/vnd.ms-works',
		'application/vnd.ms-wpl',
		'application/vnd.ms-xpsdocument',
		'application/vnd.multiad.creator',
		'application/vnd.multiad.creator.cif',
		'application/vnd.musician',
		'application/vnd.music-niff',
		'application/vnd.muvee.style',
		'application/vnd.ncd.control',
		'application/vnd.ncd.reference',
		'application/vnd.nervana',
		'application/vnd.netfpx',
		'application/vnd.neurolanguage.nlu',
		'application/vnd.noblenet-directory',
		'application/vnd.noblenet-sealer',
		'application/vnd.noblenet-web',
		'application/vnd.nokia.catalogs',
		'application/vnd.nokia.conml+wbxml',
		'application/vnd.nokia.conml+xml',
		'application/vnd.nokia.iptv.config+xml',
		'application/vnd.nokia.isds-radio-presets',
		'application/vnd.nokia.landmarkcollection+xml',
		'application/vnd.nokia.landmark+wbxml',
		'application/vnd.nokia.landmark+xml',
		'application/vnd.nokia.ncd',
		'application/vnd.nokia.n-gage.ac+xml',
		'application/vnd.nokia.n-gage.data',
		'application/vnd.nokia.n-gage.symbian.install',
		'application/vnd.nokia.pcd+wbxml',
		'application/vnd.nokia.pcd+xml',
		'application/vnd.nokia.radio-preset',
		'application/vnd.nokia.radio-presets',
		'application/vnd.novadigm.edm',
		'application/vnd.novadigm.edx',
		'application/vnd.novadigm.ext',
		'application/vnd.oasis.opendocument.chart',
		'application/vnd.oasis.opendocument.chart-template',
		'application/vnd.oasis.opendocument.database',
		'application/vnd.oasis.opendocument.formula',
		'application/vnd.oasis.opendocument.formula-template',
		'application/vnd.oasis.opendocument.graphics',
		'application/vnd.oasis.opendocument.graphics-template',
		'application/vnd.oasis.opendocument.image',
		'application/vnd.oasis.opendocument.image-template',
		'application/vnd.oasis.opendocument.presentation',
		'application/vnd.oasis.opendocument.presentation-template',
		'application/vnd.oasis.opendocument.spreadsheet',
		'application/vnd.oasis.opendocument.spreadsheet-template',
		'application/vnd.oasis.opendocument.text',
		'application/vnd.oasis.opendocument.text-master',
		'application/vnd.oasis.opendocument.text-template',
		'application/vnd.oasis.opendocument.text-web',
		'application/vnd.obn',
		'application/vnd.olpc-sugar',
		'application/vnd.oma.bcast.associated-procedure-parameter+xml',
		'application/vnd.oma.bcast.drm-trigger+xml',
		'application/vnd.oma.bcast.imd+xml',
		'application/vnd.oma.bcast.ltkm',
		'application/vnd.oma.bcast.notification+xml',
		'application/vnd.oma.bcast.provisioningtrigger',
		'application/vnd.oma.bcast.sgboot',
		'application/vnd.oma.bcast.sgdd+xml',
		'application/vnd.oma.bcast.sgdu',
		'application/vnd.oma.bcast.simple-symbol-container',
		'application/vnd.oma.bcast.smartcard-trigger+xml',
		'application/vnd.oma.bcast.sprov+xml',
		'application/vnd.oma.bcast.stkm',
		'application/vnd.oma.dcd',
		'application/vnd.oma.dcdc',
		'application/vnd.oma.dd2+xml',
		'application/vnd.oma.drm.risd+xml',
		'application/vnd.omads-email+xml',
		'application/vnd.omads-file+xml',
		'application/vnd.omads-folder+xml',
		'application/vnd.oma.group-usage-list+xml',
		'application/vnd.omaloc-supl-init',
		'application/vnd.oma.poc.detailed-progress-report+xml',
		'application/vnd.oma.poc.final-report+xml',
		'application/vnd.oma.poc.groups+xml',
		'application/vnd.oma.poc.invocation-descriptor+xml',
		'application/vnd.oma.poc.optimized-progress-report+xml',
		'application/vnd.oma-scws-config',
		'application/vnd.oma-scws-http-request',
		'application/vnd.oma-scws-http-response',
		'application/vnd.oma.xcap-directory+xml',
		'application/vnd.openofficeorg.extension',
		'application/vnd.openxmlformats-officedocument.presentationml.presentation',
		'application/vnd.openxmlformats-officedocument.presentationml.slide',
		'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
		'application/vnd.openxmlformats-officedocument.presentationml.template',
		'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
		'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
		'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
		'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
		'application/vnd.osa.netdeploy',
		'application/vnd.osgi.bundle',
		'application/vnd.osgi.dp',
		'application/vnd.otps.ct-kip+xml',
		'application/vnd.palm',
		'application/vnd.paos.xml',
		'application/vnd.pg.format',
		'application/vnd.pg.osasli',
		'application/vnd.piaccess.application-licence',
		'application/vnd.picsel',
		'application/vnd.poc.group-advertisement+xml',
		'application/vnd.pocketlearn',
		'application/vnd.powerbuilder6',
		'application/vnd.powerbuilder6-s',
		'application/vnd.powerbuilder7',
		'application/vnd.powerbuilder75',
		'application/vnd.powerbuilder75-s',
		'application/vnd.powerbuilder7-s',
		'application/vnd.preminet',
		'application/vnd.previewsystems.box',
		'application/vnd.proteus.magazine',
		'application/vnd.publishare-delta-tree',
		'application/vnd.pvi.ptid1',
		'application/vnd.pwg-multiplexed',
		'application/vnd.pwg-xhtml-print+xml',
		'application/vnd.qualcomm.brew-app-res',
		'application/vnd.quark.quarkxpress',
		'application/vnd.rapid',
		'application/vnd.recordare.musicxml',
		'application/vnd.recordare.musicxml+xml',
		'application/vnd.renlearn.rlprint',
		'application/vnd.rim.cod',
		'application/vnd.rn-realmedia',
		'application/vnd.route66.link66+xml',
		'application/vnd.ruckus.download',
		'application/vnd.s3sms',
		'application/vnd.sbm.cid',
		'application/vnd.sbm.mid2',
		'application/vnd.scribus',
		'application/vnd.sealed.3df',
		'application/vnd.sealed.csf',
		'application/vnd.sealed.doc',
		'application/vnd.sealed.eml',
		'application/vnd.sealedmedia.softseal.html',
		'application/vnd.sealedmedia.softseal.pdf',
		'application/vnd.sealed.mht',
		'application/vnd.sealed.net',
		'application/vnd.sealed.ppt',
		'application/vnd.sealed.tiff',
		'application/vnd.sealed.xls',
		'application/vnd.seemail',
		'application/vnd.sema',
		'application/vnd.semd',
		'application/vnd.semf',
		'application/vnd.shana.informed.formdata',
		'application/vnd.shana.informed.formtemplate',
		'application/vnd.shana.informed.interchange',
		'application/vnd.shana.informed.package',
		'application/vnd.simtech-mindmapper',
		'application/vnd.smaf',
		'application/vnd.smart.teacher',
		'application/vnd.software602.filler.form+xml',
		'application/vnd.software602.filler.form-xml-zip',
		'application/vnd.solent.sdkm+xml',
		'application/vnd.spotfire.dxp',
		'application/vnd.spotfire.sfs',
		'application/vnd.sss-cod',
		'application/vnd.sss-dtf',
		'application/vnd.sss-ntf',
		'application/vnd.stardivision.calc',
		'application/vnd.stardivision.draw',
		'application/vnd.stardivision.impress',
		'application/vnd.stardivision.math',
		'application/vnd.stardivision.writer',
		'application/vnd.stardivision.writer-global',
		'application/vnd.street-stream',
		'application/vnd.sun.wadl+xml',
		'application/vnd.sun.xml.calc',
		'application/vnd.sun.xml.calc.template',
		'application/vnd.sun.xml.draw',
		'application/vnd.sun.xml.draw.template',
		'application/vnd.sun.xml.impress',
		'application/vnd.sun.xml.impress.template',
		'application/vnd.sun.xml.math',
		'application/vnd.sun.xml.writer',
		'application/vnd.sun.xml.writer.global',
		'application/vnd.sun.xml.writer.template',
		'application/vnd.sus-calendar',
		'application/vnd.svd',
		'application/vnd.swiftview-ics',
		'application/vnd.symbian.install',
		'application/vnd.syncml.dm.notification',
		'application/vnd.syncml.dm+wbxml',
		'application/vnd.syncml.dm+xml',
		'application/vnd.syncml.ds.notification',
		'application/vnd.syncml+xml',
		'application/vnd.tao.intent-module-archive',
		'application/vnd.tcpdump.pcap',
		'application/vnd.tmobile-livetv',
		'application/vnd.trid.tpt',
		'application/vnd.triscape.mxs',
		'application/vnd.trueapp',
		'application/vnd.truedoc',
		'application/vnd.ufdl',
		'application/vnd.uiq.theme',
		'application/vnd.umajin',
		'application/vnd.unity',
		'application/vnd.uoml+xml',
		'application/vnd.uplanet.alert',
		'application/vnd.uplanet.alert-wbxml',
		'application/vnd.uplanet.bearer-choice',
		'application/vnd.uplanet.bearer-choice-wbxml',
		'application/vnd.uplanet.cacheop',
		'application/vnd.uplanet.cacheop-wbxml',
		'application/vnd.uplanet.channel',
		'application/vnd.uplanet.channel-wbxml',
		'application/vnd.uplanet.list',
		'application/vnd.uplanet.listcmd',
		'application/vnd.uplanet.listcmd-wbxml',
		'application/vnd.uplanet.list-wbxml',
		'application/vnd.uplanet.signal',
		'application/vnd.vcx',
		'application/vnd.vd-study',
		'application/vnd.vectorworks',
		'application/vnd.vidsoft.vidconference',
		'application/vnd.visio',
		'application/vnd.visionary',
		'application/vnd.vividence.scriptfile',
		'application/vnd.vsf',
		'application/vnd.wap.sic',
		'application/vnd.wap.slc',
		'application/vnd.wap.wbxml',
		'application/vnd.wap.wmlc',
		'application/vnd.wap.wmlscriptc',
		'application/vnd.webturbo',
		'application/vnd.wfa.wsc',
		'application/vnd.wmc',
		'application/vnd.wmf.bootstrap',
		'application/vnd.wordperfect',
		'application/vnd.wqd',
		'application/vnd.wrq-hp3000-labelled',
		'application/vnd.wt.stf',
		'application/vnd.wv.csp+wbxml',
		'application/vnd.wv.csp+xml',
		'application/vnd.wv.ssp+xml',
		'application/vnd.xara',
		'application/vnd.xfdl',
		'application/vnd.xfdl.webform',
		'application/vnd.xmi+xml',
		'application/vnd.xmpie.cpkg',
		'application/vnd.xmpie.dpkg',
		'application/vnd.xmpie.plan',
		'application/vnd.xmpie.ppkg',
		'application/vnd.xmpie.xlim',
		'application/vnd.yamaha.hv-dic',
		'application/vnd.yamaha.hv-script',
		'application/vnd.yamaha.hv-voice',
		'application/vnd.yamaha.openscoreformat',
		'application/vnd.yamaha.openscoreformat.osfpvg+xml',
		'application/vnd.yamaha.smaf-audio',
		'application/vnd.yamaha.smaf-phrase',
		'application/vnd.yellowriver-custom-menu',
		'application/vnd.zul',
		'application/vnd.zzazz.deck+xml',
		'application/voicexml+xml',
		'application/watcherinfo+xml',
		'application/whoispp-query',
		'application/whoispp-response',
		'application/winhlp',
		'application/wita',
		'application/wordperfect5.1',
		'application/wsdl+xml',
		'application/wspolicy+xml',
		'application/x-123',
		'application/x400-bp',
		'application/x-7z-compressed',
		'application/x-abiword',
		'application/x-ace-compressed',
		'application/x-adobe-indesign',
		'application/x-adobe-indesign-interchange',
		'application/x-apple-diskimage',
		'application/x-appleworks',
		'application/x-archive',
		'application/x-arj',
		'application/x-authorware-bin',
		'application/x-authorware-map',
		'application/x-authorware-seg',
		'application/x-axcrypt',
		'application/x-bcpio',
		'application/x-berkeley-db',
		'application/x-berkeley-db; format=btree',
		'application/x-berkeley-db; format=btree; version=2',
		'application/x-berkeley-db; format=btree; version=3',
		'application/x-berkeley-db; format=btree; version=4',
		'application/x-berkeley-db; format=hash',
		'application/x-berkeley-db; format=hash; version=2',
		'application/x-berkeley-db; format=hash; version=3',
		'application/x-berkeley-db; format=hash; version=4',
		'application/x-berkeley-db; format=hash; version=5',
		'application/x-berkeley-db; format=log',
		'application/x-berkeley-db; format=queue',
		'application/x-bibtex-text-file',
		'application/x-bittorrent',
		'application/x-bplist',
		'application/x-bzip',
		'application/x-bzip2',
		'application/xcap-att+xml',
		'application/xcap-caps+xml',
		'application/xcap-el+xml',
		'application/xcap-error+xml',
		'application/xcap-ns+xml',
		'application/x-cdlink',
		'application/x-chat',
		'application/x-chess-pgn',
		'application/x-chrome-package',
		'application/x-compress',
		'application/xcon-conference-info-diff+xml',
		'application/xcon-conference-info+xml',
		'application/x-coredump',
		'application/x-corelpresentations',
		'application/x-cpio',
		'application/x-csh',
		'application/x-debian-package',
		'application/x-dex',
		'application/x-director',
		'application/x-doom',
		'application/x-dosexec',
		'application/x-dtbncx+xml',
		'application/x-dtbook+xml',
		'application/x-dtbresource+xml',
		'application/x-dvi',
		'application/x-elc',
		'application/x-elf',
		'application/x-emf',
		'application/xenc+xml',
		'application/x-executable',
		'application/x-fictionbook+xml',
		'application/x-filemaker',
		'application/x-font-adobe-metric',
		'application/x-font-bdf',
		'application/x-font-dos',
		'application/x-font-framemaker',
		'application/x-font-ghostscript',
		'application/x-font-libgrx',
		'application/x-font-linux-psf',
		'application/x-font-otf',
		'application/x-font-pcf',
		'application/x-font-printer-metric',
		'application/x-font-snf',
		'application/x-font-speedo',
		'application/x-font-sunos-news',
		'application/x-font-ttf',
		'application/x-font-type1',
		'application/x-font-vfont',
		'application/x-foxmail',
		'application/x-futuresplash',
		'application/x-gnucash',
		'application/x-gnumeric',
		'application/x-grib',
		'application/x-gtar',
		'application/x-hdf',
		'application/xhtml-voice+xml',
		'application/xhtml+xml',
		'application/x-hwp',
		'application/x-ibooks+zip',
		'application/x-isatab',
		'application/x-isatab-assay',
		'application/x-isatab-investigation',
		'application/x-iso9660-image',
		'application/x-itunes-ipa',
		'application/x-java-jnilib',
		'application/x-java-jnlp-file',
		'application/x-java-pack200',
		'application/x-kdelnk',
		'application/x-killustrator',
		'application/x-latex',
		'application/x-lha',
		'application/x-lharc',
		'application/x-matlab-data',
		'application/x-matroska',
		'application/xml',
		'application/xml-dtd',
		'application/xml-external-parsed-entity',
		'application/x-mobipocket-ebook',
		'application/xmpp+xml',
		'application/x-msaccess',
		'application/x-ms-application',
		'application/x-msbinder',
		'application/x-mscardfile',
		'application/x-msclip',
		'application/x-msdownload',
		'application/x-msdownload; format=pe',
		'application/x-msdownload; format=pe32',
		'application/x-msdownload; format=pe64',
		'application/x-msdownload; format=pe-arm7',
		'application/x-msdownload; format=pe-armLE',
		'application/x-msdownload; format=pe-itanium',
		'application/x-ms-installer',
		'application/x-msmediaview',
		'application/x-msmetafile',
		'application/x-msmoney',
		'application/x-mspublisher',
		'application/x-msschedule',
		'application/x-msterminal',
		'application/x-ms-wmd',
		'application/x-ms-wmz',
		'application/x-mswrite',
		'application/x-ms-xbap',
		'application/x-mysql-db',
		'application/x-mysql-misam-compressed-index',
		'application/x-mysql-misam-data',
		'application/x-mysql-misam-index',
		'application/x-mysql-table-definition',
		'application/x-netcdf',
		'application/x-object',
		'application/xop+xml',
		'application/x-pkcs12',
		'application/x-pkcs7-certificates',
		'application/x-pkcs7-certreqresp',
		'application/x-project',
		'application/x-prt',
		'application/x-quattro-pro',
		'application/xquery',
		'application/x-rar-compressed',
		'application/x-roxio-toast',
		'application/x-rpm',
		'application/x-sas',
		'application/x-sas-access',
		'application/x-sas-audit',
		'application/x-sas-backup',
		'application/x-sas-catalog',
		'application/x-sas-data',
		'application/x-sas-data-index',
		'application/x-sas-dmdb',
		'application/x-sas-fdb',
		'application/x-sas-itemstor',
		'application/x-sas-mddb',
		'application/x-sas-program-data',
		'application/x-sas-putility',
		'application/x-sas-transport',
		'application/x-sas-utility',
		'application/x-sas-view',
		'application/x-sc',
		'application/x-sh',
		'application/x-shar',
		'application/x-sharedlib',
		'application/x-shockwave-flash',
		'application/x-silverlight-app',
		'application/xslfo+xml',
		'application/xslt+xml',
		'application/xspf+xml',
		'application/x-sqlite3',
		'application/x-staroffice-template',
		'application/x-stuffit',
		'application/x-stuffitx',
		'application/x-sv4cpio',
		'application/x-sv4crc',
		'application/x-tar',
		'application/x-tex',
		'application/x-texinfo',
		'application/x-tex-tfm',
		'application/x-tika-iworks-protected',
		'application/x-tika-java-enterprise-archive',
		'application/x-tika-java-web-archive',
		'application/x-tika-msoffice',
		'application/x-tika-msoffice-embedded',
		'application/x-tika-msoffice-embedded; format=comp_obj',
		'application/x-tika-msoffice-embedded; format=ole10_native',
		'application/x-tika-msworks-spreadsheet',
		'application/x-tika-old-excel',
		'application/x-tika-ooxml',
		'application/x-tika-ooxml-protected',
		'application/x-tika-staroffice',
		'application/x-tika-unix-dump',
		'application/x-tika-visio-ooxml',
		'application/x-uc2-compressed',
		'application/x-ustar',
		'application/x-vhd',
		'application/x-vmdk',
		'application/xv+xml',
		'application/x-wais-source',
		'application/x-webarchive',
		'application/x-x509-ca-cert',
		'application/x-xfig',
		'application/x-xmind',
		'application/x-xpinstall',
		'application/x-xz',
		'application/x-zoo',
		'application/zip',
		'audio/32kadpcm',
		'audio/3gpp',
		'audio/3gpp2',
		'audio/ac3',
		'audio/adpcm',
		'audio/amr',
		'audio/amr-wb',
		'audio/amr-wb+',
		'audio/asc',
		'audio/basic',
		'audio/bv16',
		'audio/bv32',
		'audio/clearmode',
		'audio/cn',
		'audio/dat12',
		'audio/dls',
		'audio/dsr-es201108',
		'audio/dsr-es202050',
		'audio/dsr-es202211',
		'audio/dsr-es202212',
		'audio/dvi4',
		'audio/eac3',
		'audio/evrc',
		'audio/evrc0',
		'audio/evrc1',
		'audio/evrcb',
		'audio/evrcb0',
		'audio/evrcb1',
		'audio/evrc-qcp',
		'audio/evrcwb',
		'audio/evrcwb0',
		'audio/evrcwb1',
		'audio/example',
		'audio/g719',
		'audio/g722',
		'audio/g7221',
		'audio/g723',
		'audio/g726-16',
		'audio/g726-24',
		'audio/g726-32',
		'audio/g726-40',
		'audio/g728',
		'audio/g729',
		'audio/g7291',
		'audio/g729d',
		'audio/g729e',
		'audio/gsm',
		'audio/gsm-efr',
		'audio/ilbc',
		'audio/l16',
		'audio/l20',
		'audio/l24',
		'audio/l8',
		'audio/lpc',
		'audio/midi',
		'audio/mobile-xmf',
		'audio/mp4',
		'audio/mp4a-latm',
		'audio/mpa',
		'audio/mpa-robust',
		'audio/mpeg',
		'audio/mpeg4-generic',
		'audio/ogg',
		'audio/opus',
		'audio/parityfec',
		'audio/pcma',
		'audio/pcma-wb',
		'audio/pcmu',
		'audio/pcmu-wb',
		'audio/prs.sid',
		'audio/qcelp',
		'audio/red',
		'audio/rtp-enc-aescm128',
		'audio/rtp-midi',
		'audio/rtx',
		'audio/smv',
		'audio/smv0',
		'audio/smv-qcp',
		'audio/speex',
		'audio/sp-midi',
		'audio/t140c',
		'audio/t38',
		'audio/telephone-event',
		'audio/tone',
		'audio/ulpfec',
		'audio/vdvi',
		'audio/vmr-wb',
		'audio/vnd.3gpp.iufp',
		'audio/vnd.4sb',
		'audio/vnd.adobe.soundbooth',
		'audio/vnd.audiokoz',
		'audio/vnd.celp',
		'audio/vnd.cisco.nse',
		'audio/vnd.cmles.radio-events',
		'audio/vnd.cns.anp1',
		'audio/vnd.cns.inf1',
		'audio/vnd.digital-winds',
		'audio/vnd.dlna.adts',
		'audio/vnd.dolby.heaac.1',
		'audio/vnd.dolby.heaac.2',
		'audio/vnd.dolby.mlp',
		'audio/vnd.dolby.mps',
		'audio/vnd.dolby.pl2',
		'audio/vnd.dolby.pl2x',
		'audio/vnd.dolby.pl2z',
		'audio/vnd.dts',
		'audio/vnd.dts.hd',
		'audio/vnd.everad.plj',
		'audio/vnd.hns.audio',
		'audio/vnd.lucent.voice',
		'audio/vnd.ms-playready.media.pya',
		'audio/vnd.nokia.mobile-xmf',
		'audio/vnd.nortel.vbk',
		'audio/vnd.nuera.ecelp4800',
		'audio/vnd.nuera.ecelp7470',
		'audio/vnd.nuera.ecelp9600',
		'audio/vnd.octel.sbc',
		'audio/vnd.qcelp',
		'audio/vnd.rhetorex.32kadpcm',
		'audio/vnd.sealedmedia.softseal.mpeg',
		'audio/vnd.vmx.cvsd',
		'audio/vorbis',
		'audio/vorbis-config',
		'audio/x-aac',
		'audio/x-adbcm',
		'audio/x-aiff',
		'audio/x-dec-adbcm',
		'audio/x-dec-basic',
		'audio/x-flac',
		'audio/x-matroska',
		'audio/x-mod',
		'audio/x-mpegurl',
		'audio/x-ms-wax',
		'audio/x-ms-wma',
		'audio/x-oggflac',
		'audio/x-oggpcm',
		'audio/x-pn-realaudio',
		'audio/x-pn-realaudio-plugin',
		'audio/x-wav',
		'chemical/x-cdx',
		'chemical/x-cif',
		'chemical/x-cmdf',
		'chemical/x-cml',
		'chemical/x-csml',
		'chemical/x-pdb',
		'chemical/x-xyz',
		'image/cgm',
		'image/example',
		'image/fits',
		'image/g3fax',
		'image/gif',
		'image/ief',
		'image/jp2',
		'image/jpeg',
		'image/jpm',
		'image/jpx',
		'image/naplps',
		'image/nitf',
		'image/png',
		'image/prs.btif',
		'image/prs.pti',
		'image/svg+xml',
		'image/t38',
		'image/tiff',
		'image/tiff-fx',
		'image/vnd.adobe.photoshop',
		'image/vnd.adobe.premiere',
		'image/vnd.cns.inf2',
		'image/vnd.djvu',
		'image/vnd.dwg',
		'image/vnd.dxf',
		'image/vnd.fastbidsheet',
		'image/vnd.fpx',
		'image/vnd.fst',
		'image/vnd.fujixerox.edmics-mmr',
		'image/vnd.fujixerox.edmics-rlc',
		'image/vnd.globalgraphics.pgb',
		'image/vnd.microsoft.icon',
		'image/vnd.mix',
		'image/vnd.ms-modi',
		'image/vnd.net-fpx',
		'image/vnd.radiance',
		'image/vnd.sealedmedia.softseal.gif',
		'image/vnd.sealedmedia.softseal.jpg',
		'image/vnd.sealed.png',
		'image/vnd.svf',
		'image/vnd.wap.wbmp',
		'image/vnd.xiff',
		'image/webp',
		'image/x-bpg',
		'image/x-cmu-raster',
		'image/x-cmx',
		'image/x-freehand',
		'image/x-jp2-codestream',
		'image/x-jp2-container',
		'image/x-ms-bmp',
		'image/x-niff',
		'image/x-pcx',
		'image/x-pict',
		'image/x-portable-anymap',
		'image/x-portable-bitmap',
		'image/x-portable-graymap',
		'image/x-portable-pixmap',
		'image/x-raw-adobe',
		'image/x-raw-canon',
		'image/x-raw-casio',
		'image/x-raw-epson',
		'image/x-raw-fuji',
		'image/x-raw-hasselblad',
		'image/x-raw-imacon',
		'image/x-raw-kodak',
		'image/x-raw-leaf',
		'image/x-raw-logitech',
		'image/x-raw-mamiya',
		'image/x-raw-minolta',
		'image/x-raw-nikon',
		'image/x-raw-olympus',
		'image/x-raw-panasonic',
		'image/x-raw-pentax',
		'image/x-raw-phaseone',
		'image/x-raw-rawzor',
		'image/x-raw-red',
		'image/x-raw-sigma',
		'image/x-raw-sony',
		'image/x-rgb',
		'image/x-xbitmap',
		'image/x-xcf',
		'image/x-xpixmap',
		'image/x-xwindowdump',
		'message/cpim',
		'message/delivery-status',
		'message/disposition-notification',
		'message/example',
		'message/external-body',
		'message/global',
		'message/global-delivery-status',
		'message/global-disposition-notification',
		'message/global-headers',
		'message/http',
		'message/imdn+xml',
		'message/news',
		'message/partial',
		'message/rfc822',
		'message/s-http',
		'message/sip',
		'message/sipfrag',
		'message/tracking-status',
		'message/vnd.si.simp',
		'message/x-emlx',
		'model/example',
		'model/iges',
		'model/mesh',
		'model/vnd.dwf',
		'model/vnd.dwfx+xps',
		'model/vnd.flatland.3dml',
		'model/vnd.gdl',
		'model/vnd.gs-gdl',
		'model/vnd.gs.gdl',
		'model/vnd.gtw',
		'model/vnd.moml+xml',
		'model/vnd.mts',
		'model/vnd.parasolid.transmit.binary',
		'model/vnd.parasolid.transmit.text',
		'model/vnd.vtu',
		'model/vrml',
		'multipart/alternative',
		'multipart/appledouble',
		'multipart/byteranges',
		'multipart/digest',
		'multipart/encrypted',
		'multipart/example',
		'multipart/form-data',
		'multipart/header-set',
		'multipart/mixed',
		'multipart/parallel',
		'multipart/related',
		'multipart/report',
		'multipart/signed',
		'multipart/voice-message',
		'text/asp',
		'text/aspdotnet',
		'text/calendar',
		'text/css',
		'text/csv',
		'text/dif+xml',
		'text/directory',
		'text/dns',
		'text/ecmascript',
		'text/enriched',
		'text/example',
		'text/html',
		'text/parityfec',
		'text/plain',
		'text/prs.fallenstein.rst',
		'text/prs.lines.tag',
		'text/red',
		'text/rfc822-headers',
		'text/richtext',
		'text/rtp-enc-aescm128',
		'text/rtx',
		'text/sgml',
		'text/t140',
		'text/tab-separated-values',
		'text/troff',
		'text/ulpfec',
		'text/uri-list',
		'text/vnd.abc',
		'text/vnd.curl',
		'text/vnd.curl.dcurl',
		'text/vnd.curl.mcurl',
		'text/vnd.curl.scurl',
		'text/vnd.dmclientscript',
		'text/vnd.esmertec.theme-descriptor',
		'text/vnd.fly',
		'text/vnd.fmi.flexstor',
		'text/vnd.graphviz',
		'text/vnd.in3d.3dml',
		'text/vnd.in3d.spot',
		'text/vnd.iptc.anpa',
		'text/vnd.iptc.newsml',
		'text/vnd.iptc.nitf',
		'text/vnd.latex-z',
		'text/vnd.motorola.reflex',
		'text/vnd.ms-mediapackage',
		'text/vnd.net2phone.commcenter.command',
		'text/vnd.si.uricatalogue',
		'text/vnd.sun.j2me.app-descriptor',
		'text/vnd.trolltech.linguist',
		'text/vnd.wap.si',
		'text/vnd.wap.sl',
		'text/vnd.wap.wml',
		'text/vnd.wap.wmlscript',
		'text/x-actionscript',
		'text/x-ada',
		'text/x-applescript',
		'text/x-asciidoc',
		'text/x-aspectj',
		'text/x-assembly',
		'text/x-awk',
		'text/x-basic',
		'text/x-cgi',
		'text/x-chdr',
		'text/x-c++hdr',
		'text/x-clojure',
		'text/x-cobol',
		'text/x-coffeescript',
		'text/x-coldfusion',
		'text/x-common-lisp',
		'text/x-csharp',
		'text/x-csrc',
		'text/x-c++src',
		'text/x-d',
		'text/x-diff',
		'text/x-eiffel',
		'text/x-emacs-lisp',
		'text/x-erlang',
		'text/x-expect',
		'text/x-forth',
		'text/x-fortran',
		'text/x-go',
		'text/x-groovy',
		'text/x-haml',
		'text/x-haskell',
		'text/x-haxe',
		'text/x-idl',
		'text/x-ini',
		'text/x-java-source',
		'text/x-jsp',
		'text/x-less',
		'text/x-lex',
		'text/x-log',
		'text/x-lua',
		'text/x-matlab',
		'text/x-ml',
		'text/x-modula',
		'text/x-objcsrc',
		'text/x-ocaml',
		'text/x-pascal',
		'text/x-perl',
		'text/x-php',
		'text/x-prolog',
		'text/x-python',
		'text/x-rexx',
		'text/x-rsrc',
		'text/x-rst',
		'text/x-ruby',
		'text/x-scala',
		'text/x-scheme',
		'text/x-sed',
		'text/x-setext',
		'text/x-sql',
		'text/x-stsrc',
		'text/x-tcl',
		'text/x-tika-text-based-message',
		'text/x-uuencode',
		'text/x-vbasic',
		'text/x-vbdotnet',
		'text/x-vbscript',
		'text/x-vcalendar',
		'text/x-vcard',
		'text/x-verilog',
		'text/x-vhdl',
		'text/x-web-markdown',
		'text/x-yacc',
		'text/x-yaml',
		'video/3gpp',
		'video/3gpp2',
		'video/3gpp-tt',
		'video/bmpeg',
		'video/bt656',
		'video/celb',
		'video/daala',
		'video/dv',
		'video/example',
		'video/h261',
		'video/h263',
		'video/h263-1998',
		'video/h263-2000',
		'video/h264',
		'video/jpeg',
		'video/jpeg2000',
		'video/mj2',
		'video/mp1s',
		'video/mp2p',
		'video/mp2t',
		'video/mp4',
		'video/mp4v-es',
		'video/mpeg',
		'video/mpeg4-generic',
		'video/mpv',
		'video/nv',
		'video/ogg',
		'video/parityfec',
		'video/pointer',
		'video/quicktime',
		'video/raw',
		'video/rtp-enc-aescm128',
		'video/rtx',
		'video/smpte292m',
		'video/theora',
		'video/ulpfec',
		'video/vc1',
		'video/vnd.cctv',
		'video/vnd.dlna.mpeg-tts',
		'video/vnd.fvt',
		'video/vnd.hns.video',
		'video/vnd.iptvforum.1dparityfec-1010',
		'video/vnd.iptvforum.1dparityfec-2005',
		'video/vnd.iptvforum.2dparityfec-1010',
		'video/vnd.iptvforum.2dparityfec-2005',
		'video/vnd.iptvforum.ttsavc',
		'video/vnd.iptvforum.ttsmpeg2',
		'video/vnd.motorola.video',
		'video/vnd.motorola.videop',
		'video/vnd.mpegurl',
		'video/vnd.ms-playready.media.pyv',
		'video/vnd.nokia.interleaved-multimedia',
		'video/vnd.nokia.videovoip',
		'video/vnd.objectvideo',
		'video/vnd.sealedmedia.softseal.mov',
		'video/vnd.sealed.mpeg1',
		'video/vnd.sealed.mpeg4',
		'video/vnd.sealed.swf',
		'video/vnd.vivo',
		'video/webm',
		'video/x-dirac',
		'video/x-f4v',
		'video/x-flc',
		'video/x-fli',
		'video/x-flv',
		'video/x-jng',
		'video/x-m4v',
		'video/x-matroska',
		'video/x-mng',
		'video/x-ms-asf',
		'video/x-msvideo',
		'video/x-ms-wm',
		'video/x-ms-wmv',
		'video/x-ms-wmx',
		'video/x-ms-wvx',
		'video/x-oggrgb',
		'video/x-ogguvs',
		'video/x-oggyuv',
		'video/x-ogm',
		'video/x-sgi-movie',
		'x-conference/x-cooltalk',
	);

	/**
	 * The extension key
	 *
	 * @var string
	 */
	protected $extKey = 'tikafal';

	/**
	 * To debug or not to debug
	 *
	 * @var bool
	 */
	protected $debug;

	/**
	 * Default constructor.
	 */
	public function __construct() {
		$objectManager =
			\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('\\TYPO3\\CMS\\Extbase\\Object\\ObjectManager');

		$this->configurationManager = $objectManager->get(
			'TYPO3\\CMS\Extbase\\Configuration\\ConfigurationManagerInterface'
		);

		$allTypoScript = $this->configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
		$this->settings = $allTypoScript['module.']['tx_tikafal.']['settings.'];
		$this->fieldmap = $this->settings['fieldmap.'];

		$this->configuration = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$this->extKey]);
		if ($this->configuration['extractor'] == 'tika' && !is_file(GeneralUtility::getFileAbsFileName($this->configuration['tikaPath'], FALSE))) {
			throw new \RuntimeException(
				'Invalid path or filename for Tika application jar.',
				1430600387
			);
		}
		$this->debug = (isset($configuration['debug']) && (bool)$configuration['debug']);
	}

	/**
	 * Returns an array of supported file types
	 * An empty array indicates all file types
	 *
	 * @return array
	 */
	public function getFileTypeRestrictions() {
		return array();
	}

	/**
	 * Gets all supported DriverTypes.
	 *
	 * Since some processors may only work for local files, and other
	 * are especially made for processing files from remote.
	 *
	 * Returns array of strings with driver names of Drivers which are
	 * supported, If the driver did not register a name, it's the class name.
	 * empty array indicates no restrictions
	 *
	 * @return array
	 */
	public function getDriverRestrictions() {
		return array(
			'Local',
		);
	}

	/**
	 * Returns the data priority of the processing Service.
	 * Defines the precedence if several processors
	 * can handle the same file.
	 *
	 * Should be between 1 and 100, 100 is more important than 1
	 *
	 * @return int
	 */
	public function getPriority() {
		return 50;
	}

	/**
	 * Returns the execution priority of the extraction Service.
	 * Should be between 1 and 100, 100 means runs as first service, 1 runs at
	 * last service.
	 *
	 * @return int
	 */
	public function getExecutionPriority() {
		return 50;
	}

	/**
	 * Checks if the given file can be processed by this Extractor
	 *
	 * @param Resource\File $file A file resource
	 *
	 * @return bool
	 */
	public function canProcess(Resource\File $file) {
		return in_array($file->getProperty('mime_type'), $this->supporteMimeTypes);
	}

	/**
	 * The actual processing TASK.
	 *
	 * Should return an array with database properties for sys_file_metadata to
	 * write
	 *
	 * @param Resource\File $file A file resource
	 * @param array $previousExtractedData Array of already extracted data
	 *
	 * @return array
	 */
	public function extractMetaData(Resource\File $file, array $previousExtractedData = array()) {
		$fileName = $file->getForLocalProcessing(FALSE);

		if ($this->configuration['extractor'] == 'solr') {
			$data = $this->extractUsingSolr($fileName);
		} else {
			$data = $this->extractMetadataUsingTika($fileName);
			$data['alternative'] = $this->extractTextUsingTika($fileName);
		}

		// Existing data has precedence over new information
		$metadata = array_merge($data, $previousExtractedData);

//		// Extract language
//		$languageMetatada = $this->getLanguage($file);
//		if (!empty($languageMetatada)) {
//			// Existing data has precedence over new information
//			$metadata = array_merge($languageMetatada, $metadata);
//		}

		return $metadata;
	}

	/**
	 * Extracts metadata from a given file using a local Apache Tika jar.
	 *
	 * @param string $file Absolute path to the file to extract metadata from.
	 *
	 * @return string Metadata extracted from the given file.
	 */
	protected function extractMetadataUsingTika($file) {

		$java = CommandUtility::getCommand('java');

		if (!$java) {
			throw new \RuntimeException(
				'Unable to find java on this system.',
				1430600421
			);
		}

		$tikaCommand = CommandUtility::getCommand('java')
		               . ' -Dfile.encoding=UTF8' // forces UTF8 output
		               . ' -jar ' . escapeshellarg(GeneralUtility::getFileAbsFileName($this->configuration['tikaPath'], FALSE))
		               . ' -j'
		               . ' ' . escapeshellarg($file);

		$shellOutput = shell_exec($tikaCommand);

		$this->log('Text Extraction using local Tika', array(
			'file' => $file,
			'tika command' => $tikaCommand,
			'shell output' => $shellOutput
		));

		return $this->mapMetadataFields(json_decode($shellOutput, TRUE));
	}

	/**
	 * Extracts content from a given file using a local Apache Tika jar.
	 *
	 * @param string $file Absolute path to the file to extract content from.
	 *
	 * @return string Content extracted from the given file.
	 */
	protected function extractTextUsingTika($file) {

		$java = CommandUtility::getCommand('java');

		if (!$java) {
			throw new \RuntimeException(
				'Unable to find java on this system.',
				1430600421
			);
		}

		$tikaCommand = CommandUtility::getCommand('java')
		               . ' -Dfile.encoding=UTF8' // forces UTF8 output
		               . ' -jar ' . escapeshellarg(GeneralUtility::getFileAbsFileName($this->configuration['tikaPath'], FALSE))
		               . ' -T'
		               . ' ' . escapeshellarg($file);

		$shellOutput = shell_exec($tikaCommand);

		$this->log('Text Extraction using local Tika', array(
			'file' => $file,
			'tika command' => $tikaCommand,
			'shell output' => $shellOutput
		));

		return $shellOutput;
	}

	/**
	 * Extracts content from a given file using a Solr server.
	 *
	 * @param string $file Absolute path to the file to extract content from.
	 *
	 * @return string Content extracted from the given file.
	 */
	protected function extractUsingSolr($file) {
		// FIXME move connection building to EXT:solr
		// currently explicitly using "new" to bypass
		// \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance() or providing a Factory

		// EM might define a different connection than already in use by
		// Index Queue
		$solr = new \Tx_Solr_SolrService(
			$this->configuration['solrHost'],
			$this->configuration['solrPort'],
			$this->configuration['solrPath'],
			$this->configuration['solrScheme']
		);

		$query = GeneralUtility::makeInstance('tx_solr_ExtractingQuery', $file);
		$query->setExtractOnly();
		$response = $solr->extract($query);

		$this->log('Text Extraction using Solr', array(
			'file' => $file,
			'solr connection' => (array)$solr,
			'query' => (array)$query,
			'response' => $response
		));

		return $response[0];
	}

	/**
	 * Map the extracted metadata fields according to the fieldmapping array
	 *
	 * @param array $fields The extracted metadata
	 *
	 * @return array
	 */
	protected function mapMetadataFields($fields) {
		$mappedFields = array();
		foreach ($this->fieldmap as $key => $value) {
			$mappedFields[$key] = '';
			$this->fieldmap[$key] = explode(',', str_replace(' ', '', $value));
		}

		foreach ($fields as $fieldName => $fieldValue) {
			foreach ($this->fieldmap as $key => $values) {
				if (in_array($fieldName, $values)) {
					$mappedFields[$key] = $fieldValue;
				}
			}
		}

		foreach ($mappedFields as $key => $value) {
			if (in_array($key, array('content_creation_date', 'content_modification_date'))) {
				$mappedFields[$key] = strtotime($value);
			}
		}

		return $mappedFields;
	}

	/**
	 * Debugs the output of a given service.
	 *
	 * @param string $serviceKey
	 * @param string $serviceSubType
	 * @param string $fileName
	 * @param array $output
	 *
	 * @return void
	 */
	protected function debugServiceOutput($serviceKey, $serviceSubType, $fileName, array $output) {
		$logPath = GeneralUtility::getFileAbsFileName('typo3temp/tx_tikafal/');
		GeneralUtility::mkdir_deep($logPath);
		$logFilename = date('Ymd-His-') . GeneralUtility::shortMD5($fileName) . '-' . GeneralUtility::shortMD5($serviceKey . $serviceSubType) . '.log';

		$content = array();
		$content[] = 'File:    ' . $fileName;
		$content[] = 'Service: ' . $serviceKey;
		$content[] = 'Subtype: ' . $serviceSubType;
		$content[] = 'Output:';
		$content[] = var_export($output, TRUE);

		@file_put_contents($logPath . $logFilename, implode(LF, $content));
	}

	/**
	 * Logs a message and optionally data to devlog
	 *
	 * @param string $message Log message
	 * @param array $data Optional data
	 *
	 * @return void
	 */
	protected function log($message, array $data = array()) {
		if (!$this->configuration['logging']) {
			return;
		}

		GeneralUtility::devLog($message, 'tika', 0, $data);
	}

}