import { EmptyElementWhenMatches } from '../helpers/extensions'
import { RefreshContextualTimeago } from '../configs/timeago'
import { datetimepicker } from './datetimepicker'

class datetimepicker2 extends datetimepicker
{
	constructor(Grocy, scopeSelector = null)
	{
		super(Grocy, scopeSelector, "datetimepicker2");
	}
}

export { datetimepicker2 }