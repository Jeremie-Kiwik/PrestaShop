<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\ExtraProperty\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Command\EditExtraPropertyDefinitionCommand;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Exception\ExtraPropertyDomainException;

/**
 * Contract for handling EditExtraPropertyDefinitionCommand.
 *
 * Implementations update the metadata fields (display labels, visibility flags, advanced options)
 * of an existing core extra property definition. Structural fields are not touched.
 */
interface EditExtraPropertyDefinitionCommandHandlerInterface
{
    /**
     * @param EditExtraPropertyDefinitionCommand $command
     *
     * @throws ExtraPropertyDomainException
     */
    public function handle(EditExtraPropertyDefinitionCommand $command): void;
}
