  public function getListSlotActions()
  {
    return <?php echo $this->asPhp(isset($this->config['list']['slot_actions']) ? $this->config['list']['slot_actions'] : array()) ?>;
    <?php unset($this->config['list']['slot_actions']) ?>
  }

  public function getListSlotName()
  {
    return <?php echo $this->asPhp(isset($this->config['list']['slot_name']) ? $this->config['list']['slot_name'] : '"actions"') ?>;
    <?php unset($this->config['list']['slot_name']) ?>
  }

  public function getNewSlotActions()
  {
    return <?php echo $this->asPhp(isset($this->config['new']['slot_actions']) ? $this->config['new']['slot_actions'] : array()) ?>;
    <?php unset($this->config['new']['slot_actions']) ?>
  }

  public function getNewSlotName()
  {
    return <?php echo $this->asPhp(isset($this->config['new']['slot_name']) ? $this->config['new']['slot_name'] : '"actions"') ?>;
    <?php unset($this->config['new']['slot_name']) ?>
  }

  public function getEditSlotActions()
  {
    return <?php echo $this->asPhp(isset($this->config['edit']['slot_actions']) ? $this->config['edit']['slot_actions'] : array()) ?>;
    <?php unset($this->config['edit']['slot_actions']) ?>
  }

  public function getEditSlotName()
  {
    return <?php echo $this->asPhp(isset($this->config['edit']['slot_name']) ? $this->config['edit']['slot_name'] : '"actions"') ?>;
    <?php unset($this->config['edit']['slot_name']) ?>
  }

  public function getShowSlotActions()
  {
    return <?php echo $this->asPhp(isset($this->config['show']['slot_actions']) ? $this->config['show']['slot_actions'] : array()) ?>;
    <?php unset($this->config['show']['slot_actions']) ?>
  }

  public function getShowSlotName()
  {
    return <?php echo $this->asPhp(isset($this->config['show']['slot_name']) ? $this->config['show']['slot_name'] : '"actions"') ?>;
    <?php unset($this->config['show']['slot_name']) ?>
  }
