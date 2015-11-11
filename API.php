<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link    http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\ShortcodeTracker;

use Piwik\DataTable;
use Piwik\DataTable\Row;
use Piwik\Piwik;
use Piwik\Plugins\Events\API as EventsAPI;
use Piwik\Plugins\ShortcodeTracker\Component\Generator;
use Piwik\Plugins\ShortcodeTracker\Component\NoCache;
use Piwik\Plugins\ShortcodeTracker\Component\ShortcodeCache;
use Piwik\Plugins\ShortcodeTracker\Component\ShortcodeValidator;
use Piwik\Plugins\ShortcodeTracker\Component\UrlValidator;
use Piwik\Plugins\ShortcodeTracker\Exception\UnableToRedirectException;
use Piwik\Plugins\ShortcodeTracker\Model\Model;
use Piwik\Plugins\SitesManager\API as SitesManagerAPI;

/**
 * API for plugin ShortcodeTracker
 *
 * @method static \Piwik\Plugins\ShortcodeTracker\API getInstance()
 */
class API extends \Piwik\Plugin\API
{
    /**
     * @var Model
     */
    private $model;

    /**
     * @var UrlValidator
     */
    private $urlValidator;

    /**
     * @var ShortcodeCache
     */
    private $cache;

    /**
     * @var Generator
     */
    private $generator;

    /**
     * @var SitesManagerAPI
     */
    private $sitesManagerAPI;

    /**
     * @var Settings
     */
    private $pluginSettings;

    /**
     * @hideForAll
     *
     * @return Model
     */
    public function getModel()
    {
        if ($this->model === null) {
            $this->model = new Model();
        }

        return $this->model;
    }

    /**
     * @hideForAll
     *
     * @param Model $model
     */
    public function setModel($model)
    {
        $this->checkUserNotAnonymous();
        $this->model = $model;
    }

    /**
     * @hideForAll
     *
     * @return UrlValidator
     */
    public function getUrlValidator()
    {
        if ($this->urlValidator === null) {
            $this->urlValidator = new UrlValidator();
        }

        return $this->urlValidator;
    }

    /**
     * @hideForAll
     *
     * @param UrlValidator $urlValidator
     */
    public function setUrlValidator($urlValidator)
    {
        $this->checkUserNotAnonymous();
        $this->urlValidator = $urlValidator;
    }

    /**
     * @hideForAll
     *
     * @return ShortcodeCache
     */
    public function getCache()
    {
        if ($this->cache === null) {
            $this->cache = new NoCache($this->getModel());
        }

        return $this->cache;
    }

    /**
     * @hideForAll
     *
     * @param Cache @cache
     */
    public function setCache(ShortcodeCache $cache)
    {
        $this->checkUserNotAnonymous();
        $this->cache = $cache;
    }

    /**
     * @hideForAll
     * @return Generator
     */
    public function getGenerator()
    {
        $this->checkUserNotAnonymous();
        if ($this->generator === null) {
            $this->generator = new Generator($this->getModel(), $this->getUrlValidator(), $this->getSitesManagerAPI());
        }

        return $this->generator;
    }

    /**
     * @hideForAll
     *
     * @param Generator $generator
     */
    public function setGenerator($generator)
    {
        $this->checkUserNotAnonymous();
        $this->generator = $generator;
    }

    /**
     * @hideForAll
     *
     * @return Settings
     */
    public function getPluginSettings()
    {
        if ($this->pluginSettings === null) {
            $this->pluginSettings = new Settings('ShortcodeTracker');
        }

        return $this->pluginSettings;
    }

    /**
     * @hideForAll
     *
     * @param Settings $pluginSettings
     */
    public function setPluginSettings($pluginSettings)
    {
        $this->checkUserNotAnonymous();
        $this->pluginSettings = $pluginSettings;
    }


    /**
     * @hideForAll
     *
     * @return SitesManagerAPI
     */
    public function getSitesManagerAPI()
    {
        $this->checkUserNotAnonymous();

        if ($this->sitesManagerAPI === null) {
            $this->sitesManagerAPI = SitesManagerAPI::getInstance();
        }

        return $this->sitesManagerAPI;
    }

    /**
     * @hideForAll
     *
     * @param SitesManagerAPI $sitesManagerAPI
     */
    public function setSitesManagerAPI($sitesManagerAPI)
    {
        $this->checkUserNotAnonymous();
        $this->sitesManagerAPI = $sitesManagerAPI;
    }

    /**
     * @return string
     */
    public function checkMinimalRequiredAccess()
    {
        Piwik::checkUserIsNotAnonymous();
    }

    /**
     * @param $url
     * @param $useExistingCodeIfAvailable
     *
     * @return bool|string
     */
    public function generateShortenedUrl($url, $useExistingCodeIfAvailable = false)
    {
        $this->checkMinimalRequiredAccess();

        $settings = $this->getPluginSettings();
        $baseUrl = $settings->getSetting(ShortcodeTracker::SHORTENER_URL_SETTING);

        $response = $this->generateShortcodeForUrl($url, $useExistingCodeIfAvailable);

        $shortcodeValidator = new ShortcodeValidator();
        if ($shortcodeValidator->validate($response)) {
            return $baseUrl . $response;
        }

        return $response;
    }

    /**
     * @param            $url
     * @param bool|false $useExistingCodeIfAvailable
     *
     * @return bool|string
     */
    public function generateShortcodeForUrl($url, $useExistingCodeIfAvailable = false)
    {

        $this->checkUserNotAnonymous();

        $shortcode = false;

        if ($useExistingCodeIfAvailable === "true") {
            $shortcode = $this->getModel()->selectShortcodeByUrl($url);
        }

        if ($shortcode === false) {
            $generator = $this->getGenerator();
            $shortcode = $generator->generateShortcode($url);
            $shortcodeIdsite = $generator->getIdSiteForUrl($url);

            if ($shortcode === false) {
                return Piwik::translate('ShortcodeTracker_unable_to_generate_shortcode');
            }

            $this->getModel()->insertShortcode($shortcode, $url, $shortcodeIdsite);
        }

        return $shortcode;

    }

    /**
     * @param $code
     *
     * @return string
     */
    public function getUrlFromShortcode($code)
    {
        $this->checkUserNotAnonymous();

        $shortcode = $this->getModel()->selectShortcodeByCode($code);

        return $shortcode ? $shortcode['url'] : Piwik::translate('ShortcodeTracker_invalid_shortcode');
    }

    /**
     * @param $code
     *
     * @throws UnableToRedirectException
     */
    public function performRedirectForShortcode($code)
    {
        $shortCode = $this->getCache()->getShortcode($code);

        Piwik::postEvent(ShortcodeTracker::TRACK_REDIRECT_VISIT_EVENT, array($shortCode));

        if ($shortCode === null) {
            throw new UnableToRedirectException(Piwik::translate('ShortcodeTracker_unable_to_perform_redirect'));
        }

        header('Location: ' . $shortCode['url']);
    }


    public function getShortcodeUsageReport($idSite, $period, $date, $segment = false, $columns = false)
    {
        $this->checkUserNotAnonymous();
        $eventsApi = EventsAPI::getInstance();

        $eventReport = $eventsApi
            ->getCategory($idSite, $period, $date, $segment);

        if ($eventReport->getRowsCount() === 0) {
            return new DataTable();
        }

        $shortcodeReportIdSubtable = $eventReport
            ->getRowFromLabel(ShortcodeTracker::REDIRECT_EVENT_CATEGORY)
            ->getIdSubDataTable();

        if ($shortcodeReportIdSubtable) {
            return $eventsApi->getNameFromCategoryId($idSite, $period, $date, $shortcodeReportIdSubtable);
        }

        return false;
    }


    protected function checkUserNotAnonymous()
    {
        Piwik::checkUserIsNotAnonymous();
    }
}