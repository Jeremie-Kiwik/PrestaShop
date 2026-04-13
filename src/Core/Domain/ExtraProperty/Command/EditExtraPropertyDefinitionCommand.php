<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Command;

/**
 * Command to update the metadata of an existing core extra property definition.
 *
 * Only metadata fields are editable (display labels, visibility flags, advanced options).
 * Structural fields (entity_name, field_name, field_type, field_scope, sql_index) are
 * intentionally excluded: changing them would require DDL alterations, which must go through
 * ExtraPropertyRegistryInterface::register() instead.
 *
 * Only '_core' fields (module_name = '') may be edited through the Back-Office UI.
 */
class EditExtraPropertyDefinitionCommand
{
    /** @var string|null */
    protected ?string $titleWording = null;

    /** @var string|null */
    protected ?string $titleDomain = null;

    /** @var string|null */
    protected ?string $descriptionWording = null;

    /** @var string|null */
    protected ?string $descriptionDomain = null;

    /** @var bool */
    protected bool $displayFront = false;

    /** @var bool */
    protected bool $displayApi = false;

    /** @var bool */
    protected bool $displayBo = true;

    /** @var bool */
    protected bool $displayGrid = false;

    /** @var int|null */
    protected ?int $gridPosition = null;

    /** @var string|null */
    protected ?string $validator = null;

    /** @var string|null */
    protected ?string $symfonyFieldType = null;

    /**
     * @param int $id Primary key of the extra property definition to update
     */
    public function __construct(protected readonly int $id)
    {
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param string|null $titleWording
     *
     * @return static
     */
    public function setTitleWording(?string $titleWording): static
    {
        $this->titleWording = $titleWording;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTitleWording(): ?string
    {
        return $this->titleWording;
    }

    /**
     * @param string|null $titleDomain
     *
     * @return static
     */
    public function setTitleDomain(?string $titleDomain): static
    {
        $this->titleDomain = $titleDomain;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTitleDomain(): ?string
    {
        return $this->titleDomain;
    }

    /**
     * @param string|null $descriptionWording
     *
     * @return static
     */
    public function setDescriptionWording(?string $descriptionWording): static
    {
        $this->descriptionWording = $descriptionWording;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescriptionWording(): ?string
    {
        return $this->descriptionWording;
    }

    /**
     * @param string|null $descriptionDomain
     *
     * @return static
     */
    public function setDescriptionDomain(?string $descriptionDomain): static
    {
        $this->descriptionDomain = $descriptionDomain;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescriptionDomain(): ?string
    {
        return $this->descriptionDomain;
    }

    /**
     * @param bool $displayFront
     *
     * @return static
     */
    public function setDisplayFront(bool $displayFront): static
    {
        $this->displayFront = $displayFront;

        return $this;
    }

    /**
     * @return bool
     */
    public function isDisplayFront(): bool
    {
        return $this->displayFront;
    }

    /**
     * @param bool $displayApi
     *
     * @return static
     */
    public function setDisplayApi(bool $displayApi): static
    {
        $this->displayApi = $displayApi;

        return $this;
    }

    /**
     * @return bool
     */
    public function isDisplayApi(): bool
    {
        return $this->displayApi;
    }

    /**
     * @param bool $displayBo
     *
     * @return static
     */
    public function setDisplayBo(bool $displayBo): static
    {
        $this->displayBo = $displayBo;

        return $this;
    }

    /**
     * @return bool
     */
    public function isDisplayBo(): bool
    {
        return $this->displayBo;
    }

    /**
     * @param bool $displayGrid
     *
     * @return static
     */
    public function setDisplayGrid(bool $displayGrid): static
    {
        $this->displayGrid = $displayGrid;

        return $this;
    }

    /**
     * @return bool
     */
    public function isDisplayGrid(): bool
    {
        return $this->displayGrid;
    }

    /**
     * @param int|null $gridPosition
     *
     * @return static
     */
    public function setGridPosition(?int $gridPosition): static
    {
        $this->gridPosition = $gridPosition;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getGridPosition(): ?int
    {
        return $this->gridPosition;
    }

    /**
     * @param string|null $validator
     *
     * @return static
     */
    public function setValidator(?string $validator): static
    {
        $this->validator = $validator;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getValidator(): ?string
    {
        return $this->validator;
    }

    /**
     * @param string|null $symfonyFieldType
     *
     * @return static
     */
    public function setSymfonyFieldType(?string $symfonyFieldType): static
    {
        $this->symfonyFieldType = $symfonyFieldType;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSymfonyFieldType(): ?string
    {
        return $this->symfonyFieldType;
    }
}
