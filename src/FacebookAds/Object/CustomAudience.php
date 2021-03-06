<?php
/**
 * Copyright 2014 Facebook, Inc.
 *
 * You are hereby granted a non-exclusive, worldwide, royalty-free license to
 * use, copy, modify, and distribute this software in source code or binary
 * form for use in connection with the web services and APIs provided by
 * Facebook.
 *
 * As with any software that integrates with the Facebook platform, your use
 * of this software is subject to the Facebook Developer Principles and
 * Policies [http://developers.facebook.com/policy/]. This copyright notice
 * shall be included in all copies or substantial portions of the software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
 * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 *
 */

namespace FacebookAds\Object;

use FacebookAds\Api;
use FacebookAds\Object\Fields\CustomAudienceFields;
use FacebookAds\Object\Values\CustomAudienceTypes;

class CustomAudience extends AbstractCrudObject {

  /**
   * @var string
   */
  const HASH_TYPE_SHA256 = 'sha256';

  /**
   * @var string[]
   **/
  protected static $fields = array(
    CustomAudienceFields::ID,
    CustomAudienceFields::ACCOUNT_ID,
    CustomAudienceFields::APPROXIMATE_COUNT,
    CustomAudienceFields::DATA_SOURCE,
    CustomAudienceFields::DELIVERY_STATUS,
    CustomAudienceFields::DESCRIPTION,
    CustomAudienceFields::LOOKALIKE_AUDIENCE_IDS,
    CustomAudienceFields::NAME,
    CustomAudienceFields::PERMISSION_FOR_ACTIONS,
    CustomAudienceFields::RULE,
    CustomAudienceFields::RETENTION_DAYS,
    CustomAudienceFields::SUBTYPE,
    CustomAudienceFields::TIME_UPDATED,
    CustomAudienceFields::OPERATION_STATUS,
    CustomAudienceFields::OPT_OUT_LINK,
    CustomAudienceFields::ORIGIN_AUDIENCE_ID,
    CustomAudienceFields::LOOKALIKE_SPEC,
  );

  /**
   * @return string
   */
  protected function getEndpoint() {
    return 'customaudiences';
  }

  /**
   * Add users to the AdCustomAudiences. There is no max on the total number of
   * users that can be added to an audience, but up to 10000 users can be added
   * at a given time.
   *
   * @param array $users
   * @param string $type
   * @return array
   */
  public function addUsers(array $users, $type) {
    $params = $this->formatParams($users, $type);
    return $this->getApi()->call(
      '/'.$this->assureId().'/users',
      Api::HTTP_METHOD_POST,
      $params)->getResponse();
  }

  /**
   * Delete users from AdCustomAudiences
   *
   * @param array $users
   * @param string $type
   * @return array
   */
  public function removeUsers(array $users, $type) {
    $params = $this->formatParams($users, $type);
    return $this->getApi()->call(
      '/'.$this->assureId().'/users',
      Api::HTTP_METHOD_DELETE,
      $params)->getResponse();
  }

  /**
   * Remove list of users decided to opt-out from all custom audiences
   *
   * @param array $users
   * @param string $type
   * @return boolean Returns true on success
   */
  public function optOutUsers(array $users, $type) {
    $params = $this->formatParams($users, $type);
    return $this->getApi()->call(
      '/'.$this->assureParentId().'/usersofanyaudience',
      Api::HTTP_METHOD_DELETE,
      $params)->getResponse();
  }

  /**
   * Take users and format them correctly for the request
   *
   * @param array $users
   * @param string $type
   * @return array
   */
  protected function formatParams(array $users, $type) {
    $payload = array(
      'schema' => $type,
      'data' => $users,
    );

    if ($type == CustomAudienceTypes::EMAIL
      || $type == CustomAudienceTypes::PHONE) {
      foreach ($users as &$user) {
        if ($type == CustomAudienceTypes::EMAIL) {
          $user = trim(strtolower($user), " \t\r\n\0\x0B.");
        }
        $user = hash(self::HASH_TYPE_SHA256, $user);
      }
    }
    return array('payload' => $payload);
  }

  /**
   * Share this AdCustomAudiences to other accounts
   *
   * @param array $act_ids Array of account IDs
   * @return boolean Returns true on success
   */
  public function addSharedAccounts($act_ids) {
    return $this->getApi()->call(
      '/'.$this->assureId().'/adaccounts',
      Api::HTTP_METHOD_POST,
      array('adaccounts' => $act_ids))->getResponse();
  }

  /**
   * Remove accounts from the shared AdCustomAudiences
   *
   * @param  array $act_ids Array of Account IDs to remove
   * @return boolean Returns true on success
   */
  public function removeSharedAccounts($act_ids) {
    return $this->getApi()->call(
      '/'.$this->assureId().'/adaccounts',
      Api::HTTP_METHOD_DELETE,
      array('adaccounts' => $act_ids))->getResponse();
  }

}
