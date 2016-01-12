<?php

namespace Drupal\Core\Form;

use Drupal\Component\Utility\NestedArray;

/**
 * API compatible yet incomplete implementation of the Drupal 8 equivalent.
 */
class FormState implements FormStateInterface
{
    /**
     * @var mixed[]
     */
    protected $form = [];

    /**
     * @var FormInterface
     */
    protected $formInstance = [];

    /**
     * @var mixed[]
     */
    protected $data = [];

    /**
     * Default constructor
     *
     * @param mixed[] $data
     *   Original $form_state array
     */
    public function __construct(&$data)
    {
        $this->data = &$data;

        if (!isset($this->data['storage'])) {
            $this->data['storage'] = [];
        }
        if (!isset($this->data['values'])) {
            $this->data['values'] = [];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setFormState(array $form_state_additions)
    {
        foreach ($form_state_additions as $key => $value) {
            $this->set($key, $value);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setCached($cache = true)
    {
        if ($cache && $this->isRequestMethodSafe()) {
            throw new \LogicException(sprintf('Form state caching on %s requests is not allowed.', $this->requestMethod));
        }

        $this->data['cache'] = $cache;
        $this->data['no_cache'] = !$cache;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isCached()
    {
        return empty($this->data['no_cache']) && !empty($this->data['cache']);
    }

    /**
     * {@inheritdoc}
     */
    public function disableCache()
    {
        $this->data['cache'] = false;
        $this->data['no_cache'] = true;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setLimitValidationErrors($limit_validation_errors)
    {
        // $form['#limit_validation_errors']
        throw new \LogicException("Not implemented yet");

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLimitValidationErrors()
    {
        // $form['#limit_validation_errors']
        throw new \LogicException("Not implemented yet");
    }

    /**
     * {@inheritdoc}
     */
    public function setMethod($method)
    {
        // $form['#method']
        throw new \LogicException("Not implemented yet");
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isMethodType($method_type)
    {
        // $form['#method']
        throw new \LogicException("Not implemented yet");
    }

    /**
     * Checks whether the request method is a "safe" HTTP method.
     *
     * http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html#sec9.1.1 defines
     * GET and HEAD as "safe" methods, meaning they SHOULD NOT have side-effects,
     * such as persisting $form_state changes.
     *
     * @return bool
     *
     * @see \Symfony\Component\HttpFoundation\Request::isMethodSafe()
     */
    protected function isRequestMethodSafe()
    {
        return in_array($_SERVER['REQUEST_METHOD'], ['GET', 'HEAD']);
    }

    /**
     * {@inheritdoc}
     */
    public function setValidationEnforced($must_validate = true)
    {
        throw new \LogicException("Not implemented yet");
    }

    /**
     * {@inheritdoc}
     */
    public function isValidationEnforced()
    {
        throw new \LogicException("Not implemented yet");
    }

    /**
     * {@inheritdoc}
     */
    public function disableRedirect($no_redirect = true)
    {
        $this->data['rebuild'] = $no_redirect;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isRedirectDisabled()
    {
        return $this->data['rebuild'];
    }

    /**
     * {@inheritdoc}
     */
    public function setStorage(array $storage)
    {
        $this->data['storage'] = $storage;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function &getStorage()
    {
        return $this->data['storage'];
    }

    /**
     * {@inheritdoc}
     */
    public function setSubmitHandlers(array $submit_handlers)
    {
        // $form['#submit']
        throw new \LogicException("Not implemented yet");

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubmitHandlers()
    {
        // $form['#submit']
        throw new \LogicException("Not implemented yet");
    }

    /**
     * {@inheritdoc}
     */
    public function setSubmitted() {
      $this->submitted = TRUE;
      return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isSubmitted() {
      return $this->submitted;
    }
  
    /**
     * {@inheritdoc}
     */
    public function setTemporary(array $temporary) {
      $this->temporary = $temporary;
      return $this;
    }
  
    /**
     * {@inheritdoc}
     */
    public function getTemporary() {
      return $this->temporary;
    }
  
    /**
     * {@inheritdoc}
     */
    public function &getTemporaryValue($key) {
      $value = &NestedArray::getValue($this->temporary, (array) $key);
      return $value;
    }
  
    /**
     * {@inheritdoc}
     */
    public function setTemporaryValue($key, $value) {
      NestedArray::setValue($this->temporary, (array) $key, $value, TRUE);
      return $this;
    }
  
    /**
     * {@inheritdoc}
     */
    public function hasTemporaryValue($key) {
      $exists = NULL;
      NestedArray::getValue($this->temporary, (array) $key, $exists);
      return $exists;
    }
  
    /**
     * {@inheritdoc}
     */
    public function setTriggeringElement($triggering_element) {
      $this->triggering_element = $triggering_element;
      return $this;
    }
  
    /**
     * {@inheritdoc}
     */
    public function &getTriggeringElement() {
      return $this->triggering_element;
    }
  
    /**
     * {@inheritdoc}
     */
    public function setValidateHandlers(array $validate_handlers) {
      $this->validate_handlers = $validate_handlers;
      return $this;
    }
  
    /**
     * {@inheritdoc}
     */
    public function getValidateHandlers() {
      return $this->validate_handlers;
    }

    /**
     * {@inheritdoc}
     */
    public function loadInclude($module, $type, $name = NULL) {
      if (!isset($name)) {
        $name = $module;
      }
      $build_info = $this->getBuildInfo();
      if (!isset($build_info['files']["$module:$name.$type"])) {
        // Only add successfully included files to the form state.
        if ($result = $this->moduleLoadInclude($module, $type, $name)) {
          $build_info['files']["$module:$name.$type"] = array(
            'type' => $type,
            'module' => $module,
            'name' => $name,
          );
          $this->setBuildInfo($build_info);
          return $result;
        }
      }
      return FALSE;
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheableArray()
    {
        throw new \LogicException("This is not implemented and won't ever be");
    }

    /**
     * {@inheritdoc}
     */
    public function setCompleteForm(array &$complete_form)
    {
        $this->form = &$complete_form;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function &getCompleteForm()
    {
        return $this->form;
    }

    /**
     * {@inheritdoc}
     */
    public function &get($property) {
      $value = &NestedArray::getValue($this->data['storage'], (array)$property);
      return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function set($property, $value)
    {
        NestedArray::setValue($this->data['storage'], (array)$property, $value, true);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function has($property) {
      $exists = NULL;
      NestedArray::getValue($this->data['storage'], (array)$property, $exists);
      return $exists;
    }

    /**
     * {@inheritdoc}
     */
    public function &getValues()
    {
        return $this->data['values'];
    }

    /**
     * {@inheritdoc}
     */
    public function &getValue($key, $default = null)
    {
        $exists = null;
        $value = &NestedArray::getValue($this->getValues(), (array)$key, $exists);
        if (!$exists) {
            $value = $default;
        }
        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function setValues(array $values)
    {
        $this->data['values'] = $values;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setValue($key, $value)
    {
        NestedArray::setValue($this->getValues(), (array)$key, $value, TRUE);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function unsetValue($key)
    {
        NestedArray::unsetValue($this->getValues(), (array)$key);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasValue($key)
    {
        $exists = null;
        $value = NestedArray::getValue($this->getValues(), (array)$key, $exists);

        return $exists && isset($value);
    }

    /**
     * {@inheritdoc}
     */
    public function isValueEmpty($key)
    {
        $exists = null;
        $value = NestedArray::getValue($this->getValues(), (array)$key, $exists);

        return !$exists || empty($value);
    }

    /**
     * {@inheritdoc}
     */
    public function setValueForElement(array $element, $value)
    {
        return $this->setValue($element['#parents'], $value);
    }

    /**
     * {@inheritdoc}
     */
    public function setRedirect($route_name, array $route_parameters = array(), array $options = array()) {
      $url = new Url($route_name, $route_parameters, $options);
      return $this->setRedirectUrl($url);
    }
  
    /**
     * {@inheritdoc}
     */
    public function setRedirectUrl(Url $url) {
      $this->redirect = $url;
      return $this;
    }

    /**
     * Sets the global status of errors.
     *
     * @param bool $errors
     *   TRUE if any form has any errors, FALSE otherwise.
     */
    protected static function setAnyErrors($errors = TRUE) {
      static::$anyErrors = $errors;
    }
  
    /**
     * {@inheritdoc}
     */
    public static function hasAnyErrors() {
      return static::$anyErrors;
    }
  
    /**
     * {@inheritdoc}
     */
    public function setErrorByName($name, $message = '') {
      if ($this->isValidationComplete()) {
        throw new \LogicException('Form errors cannot be set after form validation has finished.');
      }
  
      $errors = $this->getErrors();
      if (!isset($errors[$name])) {
        $record = TRUE;
        $limit_validation_errors = $this->getLimitValidationErrors();
        if ($limit_validation_errors !== NULL) {
          $record = FALSE;
          foreach ($limit_validation_errors as $section) {
            // Exploding by '][' reconstructs the element's #parents. If the
            // reconstructed #parents begin with the same keys as the specified
            // section, then the element's values are within the part of
            // $form_state->getValues() that the clicked button requires to be
            // valid, so errors for this element must be recorded. As the exploded
            // array will all be strings, we need to cast every value of the
            // section array to string.
            if (array_slice(explode('][', $name), 0, count($section)) === array_map('strval', $section)) {
              $record = TRUE;
              break;
            }
          }
        }
        if ($record) {
          $errors[$name] = $message;
          $this->errors = $errors;
          static::setAnyErrors();
        }
      }
  
      return $this;
    }
  
    /**
     * {@inheritdoc}
     */
    public function setError(array &$element, $message = '') {
      $this->setErrorByName(implode('][', $element['#parents']), $message);
      return $this;
    }
  
    /**
     * {@inheritdoc}
     */
    public function clearErrors() {
      $this->errors = [];
      static::setAnyErrors(FALSE);
    }
  
    /**
     * {@inheritdoc}
     */
    public function getError(array $element) {
      if ($errors = $this->getErrors()) {
        $parents = array();
        foreach ($element['#parents'] as $parent) {
          $parents[] = $parent;
          $key = implode('][', $parents);
          if (isset($errors[$key])) {
            return $errors[$key];
          }
        }
      }
    }
  
    /**
     * {@inheritdoc}
     */
    public function getErrors() {
      return $this->errors;
    }
  
    /**
     * {@inheritdoc}
     */
    public function setRebuild($rebuild = TRUE) {
      $this->rebuild = $rebuild;
      return $this;
    }
  
    /**
     * {@inheritdoc}
     */
    public function isRebuilding() {
      return $this->rebuild;
    }
  
    /**
     * {@inheritdoc}
     */
    public function prepareCallback($callback) {
      if (is_string($callback) && substr($callback, 0, 2) == '::') {
        $callback = [$this->getFormObject(), substr($callback, 2)];
      }
      return $callback;
    }

    /**
     * {@inheritdoc}
     */
    public function setFormObject(FormInterface $form_object)
    {
        $this->formInstance = $form_object;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFormObject()
    {
        return $this->formInstance;
    }

    /**
     * Wraps ModuleHandler::loadInclude().
     */
    protected function moduleLoadInclude($module, $type, $name = null)
    {
        return module_load_include($type, $module, $name);
    }
}
